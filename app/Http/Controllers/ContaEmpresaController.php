<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContaEmpresa;
use App\Models\PlanoConta;
use App\Models\ItemContaEmpresa;
use Dompdf\Dompdf;

class ContaEmpresaController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:contas_empresa_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:contas_empresa_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:contas_empresa_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:contas_empresa_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){
        $local_id = $request->local_id;
        $data = ContaEmpresa::
        where('empresa_id', $request->empresa_id)
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->get();

        return view('conta_empresa.index', compact('data'));
    }

    public function create(Request $request){

        $countPlanos = PlanoConta::where('empresa_id', $request->empresa_id)->count();
        if($countPlanos == 0){
            session()->flash('flash_warning', 'Defina o plano de contas');
            return redirect()->route('plano-contas.index');
        }
        return view('conta_empresa.create');
    }

    public function edit(Request $request, $id){
        $item = ContaEmpresa::findOrFail($id);

        __validaObjetoEmpresa($item);
        $countPlanos = PlanoConta::where('empresa_id', $request->empresa_id)->count();
        if($countPlanos == 0){
            session()->flash('flash_warning', 'Defina o plano de contas');
            return redirect()->route('plano-contas.index');
        }
        return view('conta_empresa.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            $request->merge([
                'saldo' => __convert_value_bd($request->saldo_inicial),
                'saldo_inicial' => __convert_value_bd($request->saldo_inicial),
            ]);
            ContaEmpresa::create($request->all());

            __createLog($request->empresa_id, 'Conta para Empresa', 'cadastrar', $request->nome);

            session()->flash("flash_success", "Conta criada com sucesso!");
            return redirect()->route('contas-empresa.index');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Conta para Empresa', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
            return redirect()->back();

        }
    }

    public function destroy($id){
        $item = ContaEmpresa::findOrFail($id);
        __validaObjetoEmpresa($item);
        try{
            $descricaoLog = $item->nome;
            $item->itens()->delete();
            $item->delete();
            __createLog(request()->empresa_id, 'Conta para Empresa', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Conta removida");
        }catch(\Exception $e){
            __createLog(request()->empresa_id, 'Conta para Empresa', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();

    }

    public function update(Request $request, $id){

        try{
            $item = ContaEmpresa::findOrFail($id);

            $request->merge([
                'saldo' => __convert_value_bd($request->saldo)
            ]);
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Conta para Empresa', 'editar', $request->nome);
            session()->flash("flash_success", "Conta atualizada!");
            return redirect()->route('contas-empresa.index');

        }catch(\Exception $e){
            __createLog($request->empresa_id, 'Conta para Empresa', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
    }

    public function show(Request $request, $id){

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $tipo = $request->tipo;

        $item = ContaEmpresa::findOrFail($id);
        __validaObjetoEmpresa($item);

        $data = ItemContaEmpresa::where('conta_id', $id)
        ->orderBy('id', 'desc')
        ->when($start_date, function ($q) use ($start_date) {
            return $q->whereDate('created_at', '>=', $start_date);
        })
        ->when($end_date, function ($q) use ($end_date) {
            return $q->whereDate('created_at', '<=', $end_date);
        })
        ->when($tipo, function ($q) use ($tipo) {
            return $q->where('tipo', $tipo);
        })
        ->paginate(50);

        return view('conta_empresa.show', compact('data', 'item'));
    }

    public function print(Request $request, $id){

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $tipo = $request->tipo;

        $item = ContaEmpresa::findOrFail($id);
        __validaObjetoEmpresa($item);

        $data = ItemContaEmpresa::where('conta_id', $id)
        ->orderBy('id', 'desc')
        ->when($start_date, function ($q) use ($start_date) {
            return $q->whereDate('created_at', '>=', $start_date);
        })
        ->when($end_date, function ($q) use ($end_date) {
            return $q->whereDate('created_at', '<=', $end_date);
        })
        ->when($tipo, function ($q) use ($tipo) {
            return $q->where('tipo', $tipo);
        })
        ->get();

        $p = view('conta_empresa.print', compact('data', 'item'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Movimentação.pdf", array("Attachment" => false));
    }

}
