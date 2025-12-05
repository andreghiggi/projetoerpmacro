<?php

namespace App\Http\Controllers;

use App\Models\Funcionamento;
use App\Models\Servico;
use App\Models\User;
use App\Models\Nfce;
use App\Models\UsuarioEmpresa;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\Agendamento;
use App\Models\ItemAgendamento;
use App\Models\CategoriaProduto;
use App\Models\Caixa;
use App\Models\MensagemAgendamentoLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ConfigGeral;
use App\Utils\WhatsAppUtil;
use App\Models\ConfiguracaoAgendamento;
use App\Utils\FilaEnvioUtil;

class AgendamentoController extends Controller
{
    protected $util;
    protected $filaEnvioUtil;

    public function __construct(WhatsAppUtil $util, FilaEnvioUtil $filaEnvioUtil)
    {
        $this->util = $util;
        $this->filaEnvioUtil = $filaEnvioUtil;

        $this->middleware('permission:agendamento_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:agendamento_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:agendamento_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:agendamento_delete', ['only' => ['destroy']]);
    }

    private function setNumeroSequencial(){
        $data = Agendamento::where('empresa_id', request()->empresa_id)
        ->where('numero_sequencial', null)
        ->get();

        $last = Agendamento::where('empresa_id', request()->empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;

        foreach($data as $d){
            $d->numero_sequencial = $numero;
            $d->save();
            $numero++;
        }
    }

    public function index(Request $request)
    {
        $this->setNumeroSequencial();
        $funcionarios = Funcionario::where('empresa_id', $request->empresa_id)
        ->where('status', 1)->get();
        $servicos = Servico::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->get();

        $funcionario_id = $request->funcionario_id;

        $data = Agendamento::where('empresa_id', $request->empresa_id)
        ->when(!empty($funcionario_id), function ($q) use ($funcionario_id) {
            return $q->where('funcionario_id', $funcionario_id);
        })
        ->limit(200)
        ->orderBy('data', 'desc')->get();
        $agendamentos = [];

        foreach($data as $item){
            $a = [
                'title' => $item->cliente->razao_social,
                'start' => $item->data . " " . $item->inicio,
                'end' => $item->data . " " . $item->termino,
                'className' => $item->getPrioridade(),
                'id' => $item->id
            ];
            array_push($agendamentos, $a);
        }

        $funcionario = null;
        if($funcionario_id){
            $funcionario = Funcionario::findOrFail($funcionario_id);
        }

        return view('agendamento.index', compact('agendamentos', 'servicos', 'funcionario', 'funcionarios'));
    }

    public function store(Request $request){
        try { 
            // dd($request->all());
            $agendamento = DB::transaction(function () use ($request) {

                $desconto = $request->desconto ? __convert_value_bd($request->desconto) : 0;
                $dataAgendamento = [
                    'funcionario_id' => $request->funcionario_id,
                    'cliente_id' => $request->cliente_id,
                    'data' => $request->data,
                    'inicio' => $request->inicio,
                    'termino' => $request->termino,
                    'prioridade' => $request->prioridade,
                    'observacao' => $request->observacao ?? "",
                    'total' => __convert_value_bd($request->total) - $desconto,
                    'desconto' => $desconto, 
                    'acrescimo' => 0, 
                    'empresa_id' => $request->empresa_id,
                    'numero_sequencial' => $this->getLastNumero($request->empresa_id)
                ];
            // dd($request->servicos);
                $agendamento = Agendamento::create($dataAgendamento);
            // dd($dataAgendamento);
                for($i=0; $i<sizeof($request->servicos); $i++){
                    $servico = Servico::findOrFail($request->servicos[$i]);
                    $dataItem = [
                        'agendamento_id' => $agendamento->id,
                        'servico_id' => $request->servicos[$i],
                        'quantidade' => 1,
                        'valor' => $servico->valor
                    ];
                    ItemAgendamento::create($dataItem);
                }
                return $agendamento;
            });

            $this->sendMessageWpp($agendamento);
            __createLog($request->empresa_id, 'Agendamento', 'cadastrar', "Data: " . __data_pt($agendamento->data) . " - cliente: " . $agendamento->cliente->info);
            session()->flash("flash_success", "Agendamento cadastrado!");
        } catch (\Exception $e) {
            // dd($e->getLine());
            __createLog($request->empresa_id, 'Agendamento', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    private function getLastNumero($empresa_id){
        $last = Agendamento::where('empresa_id', $empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;
        return $numero;
    }

    private function sendMessageWpp($agendamento){
        try{
            $configuracaoAgendamento = ConfiguracaoAgendamento::where('empresa_id', $agendamento->empresa_id)
            ->first();

            if($configuracaoAgendamento != null && $configuracaoAgendamento->token_whatsapp && $agendamento->cliente->telefone){
                $mensagem = $this->montaTextoAgendamento($agendamento);
                $telefone = "55".preg_replace('/[^0-9]/', '', $agendamento->cliente->telefone);
                $retorno = $this->util->sendMessageWithToken($telefone, $mensagem, $agendamento->empresa_id, $configuracaoAgendamento->token_whatsapp);
                // $retorno = json_decode($retorno);
                $retorno = json_decode($retorno);
                if($retorno->success){
                    MensagemAgendamentoLog::create([
                        'empresa_id' => $agendamento->empresa_id,
                        'mensagem' => $mensagem,
                        'cliente_id' => $agendamento->cliente_id
                    ]);
                }
            }
        } catch (\Exception $e) {
            // dd($e->getLine());
        }
    }

    private function montaTextoAgendamento($agendamento){
        $texto = "Olá, ". $agendamento->cliente->razao_social . ", já recebemos seu agendamento, muito obrigado!\n\n";

        $texto .= "#$agendamento->numero_sequencial\n\n";

        $texto .= "SERVIÇOS\n";
        foreach($agendamento->itens as $i){
            if($i->servico){
                $texto .= number_format($i->quantidade, 0) . "X ";

                $texto .= " " . $i->servico->nome;
                $texto .= " R$ " . __moeda($i->valor);
                $texto .= " = R$ " . __moeda($i->valor);
                $texto .= "\n";

            }
        }

        $texto .= "\nSubtotal: " . __moeda($agendamento->itens->sum('valor'));
        $texto .= "\nDesconto: " . __moeda($agendamento->desconto);
        $texto .= "\nTotal: " . __moeda($agendamento->total);

        return $texto;
    }


    public function show($id){
        $item = Agendamento::findOrFail($id);
        return view('agendamento.show', compact('item'));
    }

    public function update(Request $request, $id){
        $item = Agendamento::findOrFail($id);
        $item->inicio = $request->inicio;
        $item->termino = $request->termino;
        $item->data = $request->data;
        $item->save();
        __createLog($request->empresa_id, 'Agendamento', 'editar', "Data: " . __data_pt($item->data) . " - cliente: " . $item->cliente->info);

        session()->flash("flash_success", "Agendamento alterado!");
        return redirect()->back();

    }

    public function updateStatus(Request $request, $id){
        $item = Agendamento::findOrFail($id);

        $this->filaEnvioUtil->adicionaAgendamentoFila($item);
        $item->status = 1;
        $item->save();
        session()->flash("flash_success", "Agendamento alterado!");
        return redirect()->route('agendamentos.index');

    }

    public function destroy($id)
    {
        $item = Agendamento::findOrFail($id);
        try {
            $descricaoLog = "Data: " . __data_pt($item->data) . " - cliente: " . $item->cliente->info;

            $item->itens()->delete();
            if($item->pedidoDelivery){
                $item->pedidoDelivery->delete();
            }
            $item->delete();
            __createLog(request()->empresa_id, 'Agendamento', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Agendamento removido!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Agendamento', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->route('agendamentos.index');
    }

    public function pdv($id){
        $agendamento = Agendamento::findOrFail($id);
        __validaObjetoEmpresa($agendamento);

        // if($item->status == 1){
        //     session()->flash("flash_warning", 'Pedido já esta finalizado');
        //     return redirect()->back();
        // }

        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }

        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)->get();

        $abertura = Caixa::where('empresa_id', request()->empresa_id)->where('usuario_id', get_id_user())
        ->where('status', 1)
        ->first();

        $config = Empresa::findOrFail(request()->empresa_id);
        if($config == null){
            session()->flash("flash_warning", "Configure antes de continuar!");
            return redirect()->route('config.index');
        }

        if($config->natureza_id_pdv == null){
            session()->flash("flash_warning", "Configure a natureza de operação padrão para continuar!");
            return redirect()->route('config.index');
        }

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        $cliente = $agendamento->cliente;
        $funcionario = $agendamento->funcionario;
        $servicos = $agendamento->itens;
        $title = 'Finalizando agendamento #' . $agendamento->id;
        $caixa = __isCaixaAberto();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        $tiposPagamento = Nfce::tiposPagamento();
        if($config != null){
            $config->tipos_pagamento_pdv = $config != null && $config->tipos_pagamento_pdv ? json_decode($config->tipos_pagamento_pdv) : [];
            $temp = [];
            if(sizeof($config->tipos_pagamento_pdv) > 0){
                foreach($tiposPagamento as $key => $t){
                    if(in_array($t, $config->tipos_pagamento_pdv)){
                        $temp[$key] = $t;
                    }
                }
                $tiposPagamento = $temp;
            }
        }

        $isVendaSuspensa = 0;

        return view('front_box.create', 
            compact('categorias', 'abertura', 'funcionarios', 'agendamento', 'servicos', 'title', 'cliente', 'funcionario', 
                'caixa', 'config', 'tiposPagamento', 'isVendaSuspensa'));

    }
}
