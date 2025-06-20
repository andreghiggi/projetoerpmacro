<?php

namespace App\Http\Controllers\API\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servico;
use App\Models\MarketPlaceConfig;
use App\Models\Funcionario;
use App\Models\CarrinhoDelivery;
use App\Models\DiaSemana;
use App\Models\Agendamento;
use App\Models\Funcionamento;
use App\Models\Interrupcoes;
use App\Models\ConfiguracaoAgendamento;

class ServicoController extends Controller
{
    public function servicoModal($hash){
        $item = Servico::where('hash_delivery', $hash)->first();

        $config = MarketPlaceConfig::where('empresa_id', $item->empresa_id)->first();

        return view('food.partials.servico_modal', 
            compact('item', 'config'))->render();
    }

    public function getAtendentes(Request $request){
        $carrinho = CarrinhoDelivery::findOrFail($request->carrinho_id);
        $funcionarios = Funcionario::where('empresa_id', $carrinho->empresa_id)
        ->where('status', 1)
        ->get();

        $dataAgendamento = $request->data_agendamento;
        $data = $this->opcoesAgendamento($dataAgendamento, $funcionarios, $carrinho);
        // return response()->json($data, 200);
        $funcionarios = [];
        $funcionariosId = [];
        foreach($data as $d){
            if(!in_array($d['funcionario_id'], $funcionariosId)){
                array_push($funcionariosId, $d['funcionario_id']);
                array_push($funcionarios, [
                    'id' => $d['funcionario_id'],
                    'nome' => $d['funcionario_nome'],
                ]);
            }
        }
        return view('food.partials.atendentes', compact('carrinho', 'funcionarios', 'data'));
    }

    private function opcoesAgendamento($dataAgendamento, $funcionarios, $carrinho){
        $diaSemanaAgendamento = DiaSemana::getDia(date('w', strtotime($dataAgendamento)));
        $tempoServico = 0;
        foreach($carrinho->itens as $i){
            if($i->servico){
                $tempoServico += ($i->servico->tempo_servico + $i->servico->tempo_adicional + 
                    $i->servico->tempo_tolerancia) * $i->quantidade;
            }
        }
        $funcionarioFuncionamento = [];
        foreach($funcionarios as $f){
            $diaAtendimento = DiaSemana::where('funcionario_id', $f->id)
            ->where('dia', 'like' , "%$diaSemanaAgendamento%")->first();
            if($diaAtendimento != null){
                $funcionamento = Funcionamento::where('funcionario_id', $f->id)
                ->where('dia_id', $diaSemanaAgendamento)->first();
                if($funcionamento){
                    $funcionarioFuncionamento[] = $funcionamento;
                }
            }
        }

        $data = [];
        $configuracaoAgendamento = ConfiguracaoAgendamento::where('empresa_id', $carrinho->empresa_id)
        ->first();
        $minutosEspaco = 0;
        if($configuracaoAgendamento != null){
            $minutosEspaco = $configuracaoAgendamento->tempo_descanso_entre_agendamento;
        }
        foreach($funcionarioFuncionamento as $funcionamento){
            $inicio = $funcionamento->inicio;
            $fim = $funcionamento->fim;

            if($dataAgendamento == date('Y-m-d')){
                $inicio = strtotime("$dataAgendamento $inicio");
                $agora = strtotime("$dataAgendamento " . date('H:i'));
                $horaAgora = date('H:i');

                if($agora > $inicio){
                    // $inicio = date('H:i', strtotime(date('Y-m-d H:i') . "+ $minutosEspaco minutes"));
                    $inicio = $this->proximoMinuto($minutosEspaco);
                    // return $inicio;
                }
            }
            
            $dif = strtotime("$dataAgendamento $fim") - strtotime("$dataAgendamento $inicio");
            $minutosDif = $dif/(60); //converte milesegundos em minutos
            $contador = $minutosDif/$tempoServico;

            $interrupcoes = Interrupcoes::where('funcionario_id', $funcionamento->funcionario_id)
            ->where('status', 1)
            ->where('dia_id', $diaSemanaAgendamento)->get();
            $inicio = strtotime("$dataAgendamento $inicio");
            for($i=0; $i<$contador; $i++){
                $fim = strtotime("+".$tempoServico." minutes", $inicio);

                $temp = [
                    'funcionario_id' => $funcionamento->funcionario_id,
                    'funcionario_nome' => $funcionamento->funcionario->nome,
                    'inicio' => date('H:i', $inicio),
                    'fim' => date('H:i', $fim),
                    'data' => $dataAgendamento,
                    'tempo_servico' => $tempoServico,
                    'dia_semana' => $diaSemanaAgendamento
                ];

                $add = true;

                $interrupcao = Interrupcoes::where('funcionario_id', $f->id)
                ->where('dia_id', $diaSemanaAgendamento)
                ->whereTime('inicio', '<=', date('H:i', $inicio))
                ->whereTime('fim', '>=', date('H:i', $inicio))
                ->first();

                if($interrupcao != null){

                    $add = false;
                }else{

                    $agendamento = Agendamento::where('funcionario_id', $f->id)
                    ->whereDate('data', $dataAgendamento)
                    ->whereTime('inicio', '<=', date('H:i', $inicio))
                    ->whereTime('termino', '>=', date('H:i', $inicio))
                    ->first();

                    if($agendamento != null){
                        $add = false;
                    }
                }

                if($add == true){
                    array_push($data, $temp);
                }

                $inicio = $fim;
            }
        }

        usort($data, function($a, $b){
            return $a['inicio'] > $b['inicio'] ? 1 : -1;
        });

        $data = array_slice($data, 0, 20);

        return $data;
    }

    private function proximoMinuto($minutosEspaco){
        $inicio = date('H:i', strtotime(date('Y-m-d H:i') . "+ $minutosEspaco minutes"));
        $minuto = date('i', strtotime($inicio));
        $hora = date('H', strtotime($inicio));

        $divisao = (int)($minuto/15);
        if($divisao == 0){
            $inicio = date('H').':15';
        }else if($divisao == 1){
            $inicio = date('H').':30';
        }
        else if($divisao == 2){
            $inicio = date('H').':45';
        }else{
            $inicio = date('H', strtotime(date('Y-m-d H:i') . "+ 1 hour")).':00';
        }
        return $inicio;
    }

}
