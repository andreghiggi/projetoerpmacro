<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlanejamentoCusto;
use App\Models\ProdutoPlanejamentoCusto;
use App\Models\ServicoPlanejamentoCusto;
use App\Models\PlanejamentoCustoLog;
use App\Models\ItemPropostaPlanejamentoCusto;
use App\Models\Cotacao;
use App\Models\ItemCotacao;
use App\Models\Empresa;
use App\Models\UnidadeMedida;
use App\Models\ProjetoCusto;
use App\Models\CustoAdmPlanejamentoCusto;
use App\Utils\UploadUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\EmailConfig;
use Mail;
use App\Utils\EmailUtil;
use Dompdf\Dompdf;

class PlanejamentoCustoController extends Controller
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
        $projeto_id = $request->get('projeto_id');

        $data = PlanejamentoCusto::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when(!empty($projeto_id), function ($query) use ($projeto_id) {
            return $query->where('projeto_id', $projeto_id);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        $projetos = ProjetoCusto::where('empresa_id', $request->empresa_id)
        ->orderBy('id', 'desc')->get();

        return view('planejamento_custo.index', compact('data', 'projetos'));
    }

    public function create(){
        $projetos = ProjetoCusto::where('empresa_id', request()->empresa_id)
        ->where('estado', '!=', 'finalizado')->orderBy('id', 'desc')->get();

        $unidades = UnidadeMedida::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        return view('planejamento_custo.create', compact('projetos', 'unidades'));
    }

    public function edit($id){
        $item = PlanejamentoCusto::findOrFail($id);
        __validaObjetoEmpresa($item);

        $projetos = ProjetoCusto::where('empresa_id', request()->empresa_id)
        ->where('estado', '!=', 'finalizado')->orderBy('id', 'desc')->get();

        $unidades = UnidadeMedida::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        return view('planejamento_custo.edit', compact('item', 'projetos', 'unidades'));
    }

    private function setNumeroSequencial(){

        $last = PlanejamentoCusto::where('empresa_id', request()->empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;

        return $numero;
    }

    public function store(Request $request){
        try{
            $file_name = "";
            if ($request->hasFile('file')) {
                $file_name = $this->uploadUtil->uploadImage($request, '/planejamento_custo_arquivos', 'file');
            }

            $request->merge([
                'arquivo' => $file_name,
                'numero_sequencial' => $this->setNumeroSequencial(),
                'usuario_id' => \Auth::user()->id,
                'desconto' => __convert_value_bd($request->desconto),
                'estado' => 'cotacao'
            ]);

            // dd($request->all());
            $item = PlanejamentoCusto::create($request->all());

            for($i=0; $i<sizeof($request->produto_id); $i++){
                ProdutoPlanejamentoCusto::create([
                    'planejamento_id' => $item->id,
                    'produto_id' => $request->produto_id[$i],
                    'quantidade' => __convert_value_bd($request->quantidade_produto[$i]),
                    'valor_unitario' => __convert_value_bd($request->valor_unitario_produto[$i]),
                    'sub_total' => __convert_value_bd($request->sub_total_produto[$i]),
                    'status' => 0,
                    'observacao' => $request->observacao_produto[$i] ?? null,
                    'espessura' => $request->espessura[$i] ?? null,
                    'largura' => $request->largura[$i] ?? null, 
                    'comprimento' => $request->comprimento[$i] ?? null,
                    'peso_especifico' => $request->peso_especifico[$i] ?? null,
                    'peso_bruto' => $request->peso_bruto[$i] ?? null,
                    'calculo' => $request->calculo[$i] ?? null
                ]);
            }

            if($request->servico_id){
                for($i=0; $i<sizeof($request->servico_id); $i++){
                    ServicoPlanejamentoCusto::create([
                        'planejamento_id' => $item->id,
                        'servico_id' => $request->servico_id[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_servico[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_servico[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_servico[$i]),
                        'status' => 0,
                        'observacao' => $request->observacao_servico[$i] ?? '',
                        'terceiro' => 0,
                    ]);
                }
            }

            if($request->servico_terceiro_id){
                for($i=0; $i<sizeof($request->servico_terceiro_id); $i++){
                    ServicoPlanejamentoCusto::create([
                        'planejamento_id' => $item->id,
                        'servico_id' => $request->servico_terceiro_id[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_servico_terceiro[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_servico_terceiro[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_servico_terceiro[$i]),
                        'status' => 0,
                        'observacao' => $request->observacao_servico_terceiro[$i] ?? '',
                        'terceiro' => 1,
                    ]);
                }
            }
            if($request->descricao_custo_adm[0] != null){
                for($i=0; $i<sizeof($request->descricao_custo_adm); $i++){
                    CustoAdmPlanejamentoCusto::create([
                        'planejamento_id' => $item->id,
                        'descricao' => $request->descricao_custo_adm[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_custo_adm[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_custo_adm[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_custo_adm[$i]),
                        'observacao' => $request->observacao_custo_adm[$i] ?? '',
                    ]);
                }
            }

            session()->flash('flash_success', 'Planejamento cadastrado com sucesso!');
            return redirect()->route('planejamento-custo.index');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível concluir o cadastro ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        $item = PlanejamentoCusto::findOrFail($id);
        __validaObjetoEmpresa($item);

        try {

            $item->produtos()->delete();
            $item->servicos()->delete();
            $item->servicosTerceiro()->delete();
            $item->custosAdm()->delete();
            $item->itensProposta()->delete();
            $item->logs()->delete();
            foreach($item->cotacoes as $c){
                $c->itens()->delete();
                $c->delete();
            }
            $item->delete();
            session()->flash("flash_success", "Planejamento removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();

    }

    public function show($id)
    {
        $item = PlanejamentoCusto::findOrFail($id);
        __validaObjetoEmpresa($item);

        return view('planejamento_custo.show', compact('item'));
    }

    public function imprimirProposta($id){
        $item = PlanejamentoCusto::findOrFail($id);
        __validaObjetoEmpresa($item);
        $config = Empresa::where('id', $item->empresa_id)->first();

        $p = view('planejamento_custo.imprimir_proposta', compact('config', 'item'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4");
        $domPdf->render();
        header("Content-Disposition: ; filename=Pedido.pdf");
        $domPdf->stream("Proposta #$item->numero_sequencial.pdf", array("Attachment" => false));
    }

    public function cotacao($id)
    {
        $item = PlanejamentoCusto::findOrFail($id);

        return view('planejamento_custo.cotacao', compact('item'));
    }

    public function preview($id)
    {
        $item = PlanejamentoCusto::findOrFail($id);

        return response()->file(public_path('/uploads/planejamento_custo_arquivos/').$item->arquivo);
    }

    public function criarProposta($id)
    {
        $item = PlanejamentoCusto::findOrFail($id);

        foreach($item->produtos as $p){
            $menorValor = ItemCotacao::where('produto_id', $p->produto_id)
            ->select('item_cotacaos.valor_unitario')
            ->join('cotacaos', 'cotacaos.id', '=', 'item_cotacaos.cotacao_id')
            ->where('cotacaos.planejamento_id', $id)
            ->where('item_cotacaos.valor_unitario', '>', 0)
            ->orderBy('item_cotacaos.valor_unitario')
            ->first();

            if($menorValor){
                $p->valor_unitario = $menorValor->valor_unitario;
                $p->sub_total = $p->quantidade * $menorValor->valor_unitario;
            }
        }
        return view('planejamento_custo.proposta', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = PlanejamentoCusto::findOrFail($id);
        try {

            $file_name = $item->arquivo;
            if ($request->hasFile('file')) {
                $file_name = $this->uploadUtil->uploadImage($request, '/planejamento_custo_arquivos', 'file');

                if($item->arquivo){
                    $this->uploadUtil->unlinkImage($item, '/planejamento_custo_arquivos', 'arquivo');
                }
            }

            $request->merge([
                'arquivo' => $file_name,
                'desconto' => __convert_value_bd($request->desconto),
            ]);

            $item->fill($request->all())->save();

            $item->produtos()->delete();
            $item->servicosTerceiro()->delete();
            $item->servicos()->delete();
            $item->custosAdm()->delete();
            for($i=0; $i<sizeof($request->produto_id); $i++){
                ProdutoPlanejamentoCusto::create([
                    'planejamento_id' => $item->id,
                    'produto_id' => $request->produto_id[$i],
                    'quantidade' => __convert_value_bd($request->quantidade_produto[$i]),
                    'valor_unitario' => __convert_value_bd($request->valor_unitario_produto[$i]),
                    'sub_total' => __convert_value_bd($request->sub_total_produto[$i]),
                    'status' => 0,
                    'observacao' => $request->observacao_produto[$i] ?? '',
                    'espessura' => $request->espessura[$i] ?? '',
                    'largura' => $request->largura[$i] ?? '', 
                    'comprimento' => $request->comprimento[$i] ?? '',
                    'peso_especifico' => $request->peso_especifico[$i] ?? null,
                    'peso_bruto' => $request->peso_bruto[$i] ?? null,
                    'calculo' => $request->calculo[$i] ?? null
                ]);
            }

            if($request->servico_id){
                for($i=0; $i<sizeof($request->servico_id); $i++){
                    ServicoPlanejamentoCusto::create([
                        'planejamento_id' => $item->id,
                        'servico_id' => $request->servico_id[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_servico[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_servico[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_servico[$i]),
                        'status' => 0,
                        'observacao' => $request->observacao_servico[$i] ?? '',
                        'terceiro' => 0,
                    ]);
                }
            }

            if($request->servico_terceiro_id){
                for($i=0; $i<sizeof($request->servico_terceiro_id); $i++){
                    ServicoPlanejamentoCusto::create([
                        'planejamento_id' => $item->id,
                        'servico_id' => $request->servico_terceiro_id[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_servico_terceiro[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_servico_terceiro[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_servico_terceiro[$i]),
                        'status' => 0,
                        'observacao' => $request->observacao_servico_terceiro[$i] ?? '',
                        'terceiro' => 1,
                    ]);
                }
            }

            if($request->descricao_custo_adm[0] != null){
                for($i=0; $i<sizeof($request->descricao_custo_adm); $i++){
                    CustoAdmPlanejamentoCusto::create([
                        'planejamento_id' => $item->id,
                        'descricao' => $request->descricao_custo_adm[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_custo_adm[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_custo_adm[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_custo_adm[$i]),
                        'observacao' => $request->observacao_custo_adm[$i] ?? '',

                    ]);
                }
            }
            session()->flash('flash_success', 'Planejamento atualizado com sucesso!');
            return redirect()->route('planejamento-custo.show', [$item->id]);
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível concluir a atualização ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function alerarEstado(Request $request, $id){
        $item = PlanejamentoCusto::findOrFail($id);
        
        PlanejamentoCustoLog::create([
            'planejamento_id' => $item->id,
            'usuario_id' => \Auth::user()->id,
            'estado_anterior' => $item->estado,
            'estado_alterado' => $request->estado_alterado,
            'observacao' => $request->observacao ?? ''
        ]);
        $item->estado = $request->estado_alterado;
        $item->save();
        session()->flash('flash_success', 'Estado alterado com sucesso!');
        return redirect()->back();

    }

    public function storeCotacao(Request $request, $id){

        $item = PlanejamentoCusto::findOrFail($id);

        try{
            DB::transaction(function () use ($request, $item) {
                $referencia = Str::random(7);

                $cotacao = Cotacao::create([
                    'empresa_id' => $item->empresa_id,
                    'fornecedor_id' => $request->fornecedor_id,
                    'hash_link' => Str::random(30),
                    'referencia' => $referencia,
                    'observacao' => $item->observacao ?? '',
                    'estado' => 'nova',
                    'planejamento_id' => $item->id
                ]);
                foreach($item->produtos as $i){
                    ItemCotacao::create([
                        'cotacao_id' => $cotacao->id,
                        'quantidade' => $i->quantidade,
                        'produto_id' => $i->produto_id,
                    ]);
                }
                __createLog($item->empresa_id, 'Cotação', 'cadastrar', $cotacao->fornecedor->info . " - #$cotacao->hash_link");
                $this->enviarEmailCotacao($cotacao);
            });
            session()->flash("flash_success", 'Cotação criada!');

        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Cotação', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    private function enviarEmailCotacao($cotacao){
        if($cotacao->fornecedor->email != ''){

            $email = $cotacao->fornecedor->email;

            $emailConfig = EmailConfig::where('empresa_id', request()->empresa_id)
            ->where('status', 1)
            ->first();
            if($emailConfig != null){

                $body = view('mail.cotacao', compact('cotacao'));
                $result = $this->emailUtil->enviaEmailPHPMailer($email, 'Envio de cotação', $body, $emailConfig);
            }else{
                Mail::send('mail.cotacao', ['cotacao' => $cotacao], function($m) use ($email){

                    $nomeEmail = env('MAIL_FROM_NAME');
                    $m->from(env('MAIL_USERNAME'), $nomeEmail);
                    $m->subject('Envio de cotação');
                    $m->to($email);
                });
            }
        }
    }

    public function propostaStore(Request $request, $id){
        $item = PlanejamentoCusto::findOrFail($id);
        try{
            $item->desconto = __convert_value_bd($request->desconto);
            $item->frete = __convert_value_bd($request->frete);
            $somaCusto = 0;
            $somaFinal = 0;

            $item->itensProposta()->delete();
            for($i=0; $i<sizeof($request->descricao_produto); $i++){

                $itemPlanejamento = $item->produtos[$i];
                $dataItem = [
                    'planejamento_id' => $id,
                    'descricao' => $request->descricao_produto[$i],
                    'quantidade' => __convert_value_bd($request->quantidade_produto[$i]),
                    'valor_unitario_custo' => __convert_value_bd($request->valor_unitario_produto[$i]),
                    'valor_unitario_final' => __convert_value_bd($request->valor_unitario_final_produto[$i]),
                    'sub_total_custo' => __convert_value_bd($request->sub_total_produto[$i]),
                    'sub_total_final' => __convert_value_bd($request->sub_total_final_produto[$i]),
                    'tipo' => 'produto',
                    'observacao' => $request->observacao_produto[$i],
                    'servico_id' => null,
                    'produto_id' => $request->produto_id[$i],
                    'terceiro' => 0,
                    'espessura' => $itemPlanejamento ? $itemPlanejamento->espessura : null,
                    'largura' => $itemPlanejamento ? $itemPlanejamento->largura : null,
                    'comprimento' => $itemPlanejamento ? $itemPlanejamento->comprimento : null,
                    'peso_especifico' => $itemPlanejamento ? $itemPlanejamento->peso_especifico : null,
                    'peso_bruto' => $itemPlanejamento ? $itemPlanejamento->peso_bruto : null,
                    'calculo' => $itemPlanejamento ? $itemPlanejamento->calculo : null
                ];

                $somaCusto += __convert_value_bd($request->sub_total_produto[$i]);
                $somaFinal += __convert_value_bd($request->sub_total_final_produto[$i]);
                ItemPropostaPlanejamentoCusto::create($dataItem);
            }

            if($request->descricao_servico != null){
                for($i=0; $i<sizeof($request->descricao_servico); $i++){
                    $dataItem = [
                        'planejamento_id' => $id,
                        'descricao' => $request->descricao_servico[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_servico[$i]),
                        'valor_unitario_custo' => __convert_value_bd($request->valor_unitario_servico[$i]),
                        'valor_unitario_final' => __convert_value_bd($request->valor_unitario_final_servico[$i]),
                        'sub_total_custo' => __convert_value_bd($request->sub_total_servico[$i]),
                        'sub_total_final' => __convert_value_bd($request->sub_total_final_servico[$i]),
                        'tipo' => 'mão de obra',
                        'observacao' => $request->observacao_servico[$i],
                        'servico_id' => $request->servico_id[$i],
                        'produto_id' => null,
                        'terceiro' => 0
                    ];

                    $somaCusto += __convert_value_bd($request->sub_total_servico[$i]);
                    $somaFinal += __convert_value_bd($request->sub_total_final_servico[$i]);
                    ItemPropostaPlanejamentoCusto::create($dataItem);
                }
            }

            if($request->descricao_servico_terceiro != null){
                for($i=0; $i<sizeof($request->descricao_servico_terceiro); $i++){
                    $dataItem = [
                        'planejamento_id' => $id,
                        'descricao' => $request->descricao_servico[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_servico_terceiro[$i]),
                        'valor_unitario_custo' => __convert_value_bd($request->valor_unitario_servico_terceiro[$i]),
                        'valor_unitario_final' => __convert_value_bd($request->valor_unitario_final_servico_terceiro[$i]),
                        'sub_total_custo' => __convert_value_bd($request->sub_total_servico_terceiro[$i]),
                        'sub_total_final' => __convert_value_bd($request->sub_total_final_servico_terceiro[$i]),
                        'tipo' => 'serviço terceiro',
                        'observacao' => $request->observacao_servico_terceiro[$i],
                        'servico_id' => $request->servico_id[$i],
                        'produto_id' => null,
                        'terceiro' => 1
                    ];

                    $somaCusto += __convert_value_bd($request->sub_total_servico_terceiro[$i]);
                    $somaFinal += __convert_value_bd($request->sub_total_final_servico_terceiro[$i]);
                    ItemPropostaPlanejamentoCusto::create($dataItem);
                }
            }

            if($request->descricao_custo_adm != null){
                for($i=0; $i<sizeof($request->descricao_custo_adm); $i++){
                    $dataItem = [
                        'planejamento_id' => $id,
                        'descricao' => $request->descricao_custo_adm[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_custo_adm[$i]),
                        'valor_unitario_custo' => __convert_value_bd($request->valor_unitario_custo_adm[$i]),
                        'valor_unitario_final' => __convert_value_bd($request->valor_unitario_final_custo_adm[$i]),
                        'sub_total_custo' => __convert_value_bd($request->sub_total_custo_adm[$i]),
                        'sub_total_final' => __convert_value_bd($request->sub_total_final_custo_adm[$i]),
                        'tipo' => 'serviço terceiro',
                        'observacao' => $request->observacao_custo_adm[$i],
                        'servico_id' => null,
                        'produto_id' => null,
                        'terceiro' => 1
                    ];

                    $somaCusto += __convert_value_bd($request->sub_total_custo_adm[$i]);
                    $somaFinal += __convert_value_bd($request->sub_total_final_custo_adm[$i]);
                    ItemPropostaPlanejamentoCusto::create($dataItem);
                }
            }

            $item->total_custo = $somaCusto;
            $item->total_final = $somaFinal;
            $item->save();

            PlanejamentoCustoLog::create([
                'planejamento_id' => $item->id,
                'usuario_id' => \Auth::user()->id,
                'estado_anterior' => '',
                'estado_alterado' => '',
                'observacao' => 'Criação de proposta'
            ]);

            session()->flash('flash_success', 'Proposta criada com sucesso!');
            return redirect()->route('planejamento-custo.show', [$item->id]);
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível concluir o cadastro ' . $e->getMessage());
            return redirect()->back();
        }

    }
}
