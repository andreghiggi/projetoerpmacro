<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empresa;
use App\Models\ContaReceber;
use App\Models\ContaPagar;
use App\Models\Notificacao;
use App\Models\ItemNfe;
use App\Models\Produto;
use App\Models\Estoque;
use App\Models\ConfigGeral;
use App\Models\Agendamento;
use App\Models\MensagemAgendamentoLog;
use App\Models\ConfiguracaoAgendamento;
use App\Utils\WhatsAppUtil;

class AlertaCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerta:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria alertas para empresas';

    /**
     * Execute the console command.
     */

    protected $whatsAppUtil;

    public function __construct(WhatsAppUtil $whatsAppUtil){
        parent::__construct();
        $this->whatsAppUtil = $whatsAppUtil;
    }

    public function handle()
    {
        $empresas = Empresa::where('status', 1)->get();

        foreach($empresas as $empresa){

            $config = ConfigGeral::where('empresa_id', $empresa->id)->first();
            $alertasAtivos = null;
            if($config != null){
                $alertasAtivos = json_decode($config->notificacoes);
            }
            // dd($alertasAtivos);
            if($alertasAtivos == null || in_array('Contas a receber', $alertasAtivos)){
                $contasReceber = ContaReceber::where('empresa_id', $empresa->id)
                ->where('status', 0)
                ->whereDate('data_vencimento', date('Y-m-d'))
                ->get();

                foreach($contasReceber as $conta){
                    $descricaoCurta = $conta->cliente->razao_social . " R$" . __moeda($conta->valor_integral);
                    $this->criaNotificacao('conta_recebers', $conta->id, $empresa, 'Conta a receber', $descricaoCurta, $conta);
                }
            }

            if($alertasAtivos == null || in_array('Contas a pagar', $alertasAtivos)){
                $contasPagar = ContaPagar::where('empresa_id', $empresa->id)
                ->where('status', 0)
                ->whereDate('data_vencimento', date('Y-m-d'))
                ->get();

                foreach($contasPagar as $conta){
                    $descricaoCurta = $conta->fornecedor->razao_social . " R$" . __moeda($conta->valor_integral);
                    $this->criaNotificacao('conta_pagars', $conta->id, $empresa, 'Conta a pagar', $descricaoCurta, $conta);
                }
            }

            if($alertasAtivos == null || in_array('Alerta de validade', $alertasAtivos)){
                $produtosComAlertaValidade = Produto::where('empresa_id', $empresa->id)
                ->where('alerta_validade', '>', 0)->get();
                foreach($produtosComAlertaValidade as $produto){
                    $date = date('Y-m-d', strtotime(date('Y-m-d'). "+$produto->alerta_validade days"));
                    $itens = ItemNfe::where('produto_id', $produto->id)
                    ->whereDate('data_vencimento', $date)
                    ->get();

                    foreach($itens as $i){
                        $descricaoCurta = $i->produto->nome;
                        $this->criaNotificacao('compras', $i->id, $empresa, 'Alerta de vencimento', $descricaoCurta, $i, 'media');
                    }
                }
            }

            if($alertasAtivos == null || in_array('Alerta de estoque', $alertasAtivos)){
                $produtosComEstoqueMinimo = Produto::where('empresa_id', $empresa->id)
                ->where('estoque_minimo', '>', 0)->get();
                foreach($produtosComEstoqueMinimo as $produto){
                    $estoque = Estoque::where('produto_id', $produto->id)->first();

                    if($estoque != null && $estoque->quantidade <= $produto->estoque_minimo){
                        $descricaoCurta = $produto->nome;
                        $this->criaNotificacao('estoques', $estoque->id, $empresa, 'Alerta de estoque', $descricaoCurta, $estoque, 'media');
                    }
                }
            }

            $configuracaoAgendamento = ConfiguracaoAgendamento::where('empresa_id', $empresa->id)
            ->first();

            if($configuracaoAgendamento != null && $configuracaoAgendamento->token_whatsapp){
                $this->criaAlertaAgendamento($configuracaoAgendamento);
            }
        }
    }

    private function criaAlertaAgendamento($config){
        $agendamentos = $this->getAgendamentosHoje($config->empresa_id);

        if($config->msg_wpp_manha && $config->msg_wpp_manha_horario){
            $dataAtual = date('Y-m-d H:i');
            $dataEnvio = date('Y-m-d ') . $config->msg_wpp_manha_horario;
            if(strtotime($dataAtual) >= strtotime($dataEnvio)){
                foreach($agendamentos as $a){

                    if($a->cliente->telefone && $a->msg_wpp_manha_horario == 0){
                        $msg = $this->criaMensagemAgendamento($a, $config->mensagem_manha);
                        if($msg != ""){
                            $telefone = "55".preg_replace('/[^0-9]/', '', $a->cliente->telefone);
                            // dd($telefone);
                            $retorno = $this->whatsAppUtil->sendMessageWithToken($telefone, $msg, $config->empresa_id, $config->token_whatsapp);
                            $retorno = json_decode($retorno);
                            if($retorno->success){
                                $a->msg_wpp_manha_horario = 1;
                                $a->save();
                                MensagemAgendamentoLog::create([
                                    'empresa_id' => $config->empresa_id,
                                    'mensagem' => $msg,
                                    'cliente_id' => $a->cliente_id
                                ]);
                            }else{
                                // dd($retorno);
                            }
                        }
                    }
                }
            }
        }
        if($config->msg_wpp_alerta){
            $dataAtual = date('Y-m-d H:i');
            foreach($agendamentos as $a){

                $dataEnvio = date('Y-m-d H:i', strtotime($a->data . " " . $a->inicio . "- $config->msg_wpp_alerta_minutos_antecedencia minutes"));

                if(strtotime($dataAtual) >= strtotime($dataEnvio)){
                    // foreach($agendamentos as $a){
                    if($a->cliente->telefone && $a->msg_wpp_alerta_horario == 0){
                        $msg = $this->criaMensagemAgendamento($a, $config->mensagem_alerta);

                        if($msg != ""){
                            $telefone = "55".preg_replace('/[^0-9]/', '', $a->cliente->telefone);
                            $retorno = $this->whatsAppUtil->sendMessageWithToken($telefone, $msg, $config->empresa_id, $config->token_whatsapp);
                            $retorno = json_decode($retorno);
                            if($retorno->success){
                                $a->msg_wpp_alerta_horario = 1;
                                $a->save();

                                MensagemAgendamentoLog::create([
                                    'empresa_id' => $config->empresa_id,
                                    'mensagem' => $msg,
                                    'cliente_id' => $a->cliente_id
                                ]);
                            }else{
                                // dd($retorno);
                            }
                        }
                    }
                    // }
                }
            }
        }
    }

    private function criaMensagemAgendamento($agendamento, $msg){
        if(strlen(trim($msg)) == 0) return "";
        $msg = str_replace("%nome%", $agendamento->cliente->razao_social, $msg);
        $msg = str_replace("%data%", __data_pt($agendamento->data, 0), $msg);
        $msg = str_replace("%hora%", substr($agendamento->inicio, 0, 5), $msg);

        $textoServico = "SERVIÇOS\n";
        foreach($agendamento->itens as $i){
            if($i->servico){
                $textoServico .= number_format($i->quantidade, 0) . "X ";

                $textoServico .= " " . $i->servico->nome;
                $textoServico .= " R$ " . __moeda($i->valor);
                $textoServico .= " = R$ " . __moeda($i->valor);
                $textoServico .= "\n";

            }
        }

        $msg = str_replace("%serviços%", $textoServico, $msg);

        return $msg;
    }

    private function getAgendamentosHoje($empresa_id){
        return Agendamento::where('empresa_id', $empresa_id)
        ->whereDate('data', date('Y-m-d'))->get();
    }

    private function criaNotificacao($tabela, $referencia, $empresa, $titulo, $descricaoCurta, $objeto, $prioridade = 'baixa'){
        $item = Notificacao::where('empresa_id', $empresa->id)
        ->where('tabela', $tabela)
        ->where('referencia', $referencia)->first();

        if($item == null){
            $descricao = $this->getDescricao($tabela, $objeto);
            Notificacao::create([
                'empresa_id' => $empresa->id,
                'tabela' => $tabela,
                'descricao' => $descricao,
                'descricao_curta' => $descricaoCurta,
                'referencia' => $referencia,
                'status' => 1,
                'por_sistema' => 1,
                'prioridade' => $prioridade, 
                'visualizada' => 0,
                'titulo' => $titulo
            ]);
        }
    }

    private function getDescricao($tabela, $item){
        if($tabela == 'conta_recebers'){
            return view('notificacao.partials.conta_receber', compact('item'));
        }
        if($tabela == 'conta_pagars'){
            return view('notificacao.partials.conta_pagar', compact('item'));
        }
        if($tabela == 'compras'){
            return view('notificacao.partials.compras', compact('item'));
        }
        if($tabela == 'estoques'){
            return view('notificacao.partials.estoques', compact('item'));
        }
    }
}
