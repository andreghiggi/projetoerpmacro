<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjetoCusto;
use App\Models\Empresa;
use App\Utils\UploadUtil;
use App\Utils\EmailUtil;
use Dompdf\Dompdf;

class ProjetoCustoController extends Controller
{
    protected $uploadUtil;
    protected $emailUtil;

    public function __construct(UploadUtil $uploadUtil, EmailUtil $emailUtil)
    {
        $this->uploadUtil = $uploadUtil;
        $this->emailUtil = $emailUtil;
        $this->middleware('permission:planejamento_custo_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:planejamento_custo_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:planejamento_custo_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:planejamento_custo_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $estado = $request->get('estado');

        $data = ProjetoCusto::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        return view('projeto_custo.index', compact('data'));
    }

    public function create(){
        return view('projeto_custo.create');
    }

    public function edit($id){
        $item = ProjetoCusto::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('projeto_custo.edit', compact('item'));
    }

    private function setNumeroSequencial(){

        $last = ProjetoCusto::where('empresa_id', request()->empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;

        return $numero;
    }

    private function setNumeroSequencialAno(){

        $last = ProjetoCusto::where('empresa_id', request()->empresa_id)
        ->orderBy('numero_sequencial_ano', 'desc')
        ->where('numero_sequencial_ano', '>', 0)->first();
        
        if($last != null){
            $anoAtual = date('Y');
            if($anoAtual != \Carbon\Carbon::parse($last->created_at)->format('Y')){
                return 1;
            }else{
             return $last->numero_sequencial_ano+1;
         }
     }else{
        return 1;
    }
}

public function store(Request $request){
    try{
        $file_name = "";
        if ($request->hasFile('file')) {
            $file_name = $this->uploadUtil->uploadImage($request, '/projeto_custo_arquivos', 'file');
        }

        $request->merge([
            'arquivo' => $file_name,
            'numero_sequencial' => $this->setNumeroSequencial(),
            'usuario_id' => \Auth::user()->id,
            'desconto' => __convert_value_bd($request->desconto),
            // 'estado' => 'cotacao',
            '_id' => $this->setNumeroSequencialAno()."-".date('Y')
        ]);

        ProjetoCusto::create($request->all());

        session()->flash('flash_success', 'Projeto cadastrado com sucesso!');
        return redirect()->route('projeto-custo.index');
    } catch (\Exception $e) {
        session()->flash('flash_error', 'Não foi possível concluir o cadastro ' . $e->getMessage());
        return redirect()->back();
    }
}

public function update(Request $request, $id)
{
    $item = ProjetoCusto::findOrFail($id);
    try {

        $file_name = $item->arquivo;
        if ($request->hasFile('file')) {
            $file_name = $this->uploadUtil->uploadImage($request, '/projeto_custo_arquivos', 'file');

            if($item->arquivo){
                $this->uploadUtil->unlinkImage($item, '/projeto_custo_arquivos', 'arquivo');
            }
        }

        $request->merge([
            'arquivo' => $file_name,
        ]);

        $item->fill($request->all())->save();

        session()->flash('flash_success', 'Projeto atualizado com sucesso!');
        return redirect()->route('projeto-custo.index');
    } catch (\Exception $e) {
        session()->flash('flash_error', 'Não foi possível concluir a atualização ' . $e->getMessage());
        return redirect()->back();
    }
}

public function destroy($id)
{
    $item = ProjetoCusto::findOrFail($id);
    __validaObjetoEmpresa($item);

    try {

        foreach($item->planejamentos as $p){
            $p->produtos()->delete();
            $p->servicos()->delete();
            $p->servicosTerceiro()->delete();
            $p->custosAdm()->delete();
            $p->itensProposta()->delete();
            $p->logs()->delete();
            foreach($p->cotacoes as $c){
                $c->itens()->delete();
                $c->delete();
            }
            $p->delete();
        }
        $item->delete();
        session()->flash("flash_success", "Planejamento removido!");
    } catch (\Exception $e) {
        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    }
    return redirect()->back();

}

public function print($id){
    $item = ProjetoCusto::findOrFail($id);
    __validaObjetoEmpresa($item);
    $config = Empresa::where('id', $item->empresa_id)->first();

    $p = view('projeto_custo.imprimir', compact('config', 'item'));

    $domPdf = new Dompdf(["enable_remote" => true]);
    $domPdf->loadHtml($p);
    $pdf = ob_get_clean();
    $domPdf->setPaper("A4");
    $domPdf->render();
    header("Content-Disposition: ; filename=Pedido.pdf");
    $domPdf->stream("Projeto #$item->_id.pdf", array("Attachment" => false));
}

}
