<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\CrmAnotacao;
use App\Models\Notificacao;
use App\Models\FilaEnvioCron;

class CrmController extends Controller
{
    public function referenciaRegistro(Request $request){
        $tipo_registro = $request->tipo_registro;
        $data = Nfe::where('empresa_id', $request->empresa_id)
        ->when($tipo_registro == 'venda', function ($q){
            return $q->where('tpNF', 1)->where('orcamento', 0);
        })
        ->when($tipo_registro == 'compra', function ($q){
            return $q->where('tpNF', 0)->where('orcamento', 0);
        })
        ->when($tipo_registro == 'orçamento', function ($q){
            return $q->where('orcamento', 1);
        })
        ->where('numero_sequencial', 'like', "%$request->pesquisa%")
        ->get();

        foreach($data as $item){
            $razaoSocial = "";
            if($item->cliente){
                $razaoSocial = $item->cliente->info;
            }
            if($item->fornecedor){
                $razaoSocial = $item->fornecedor->info;
            }
            $item->descricao = $item->numero_sequencial . " - " . $razaoSocial . " | R$" . __moeda($item->total);
        }
        return response()->json($data, 200);
    }

    public function modalLog(Request $request){
        $item = FilaEnvioCron::findOrFail($request->id);
        return view('mensagem_padrao_crm.partials.modal', compact('item'));
    }

    public function modal(Request $request){
        $data = CrmAnotacao::where('empresa_id', $request->empresa_id)
        ->whereDate('data_retorno', date('Y-m-d'))
        ->get();
        $this->criaNotificacao($data);
        return view('crm.partials.modal', compact('data'));
    }

    private function criaNotificacao($data){
        foreach($data as $item){
            $notificacao = Notificacao::where('empresa_id', $item->empresa_id)
            ->where('tabela', 'crm_anotacaos')
            ->where('referencia', $item->id)->first();

            if($notificacao == null){

                if($item->cliente){
                    $descricaoCurta = $item->cliente->info;
                }
                if($item->fornecedor){
                    $descricaoCurta = $item->fornecedor->info;
                }
                $descricao = view('notificacao.partials.crm', compact('item'));

                Notificacao::create([
                    'empresa_id' => $item->empresa_id,
                    'tabela' => 'crm_anotacaos',
                    'descricao' => $descricao,
                    'descricao_curta' => $descricaoCurta,
                    'referencia' => $item->id,
                    'status' => 1,
                    'por_sistema' => 1,
                    'prioridade' => 'alta', 
                    'visualizada' => 0,
                    'titulo' => 'CRM'
                ]);
            }
        }
    }
}
