<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashBackConfig;

class CashBackConfigController extends Controller
{
    public function index(Request $request){
        $item = CashBackConfig::
        where('empresa_id', $request->empresa_id)
        ->first();

        return view('cash_back_config.index', compact('item'));
    }

    public function store(Request $request){
        $this->_validate($request);

        $item = CashBackConfig::
        where('empresa_id', $request->empresa_id)
        ->first();

        try{
            if($item != null){

                $item->valor_percentual = __convert_value_bd($request->valor_percentual);
                $item->valor_minimo_venda = __convert_value_bd($request->valor_minimo_venda);
                $item->percentual_maximo_venda = __convert_value_bd($request->percentual_maximo_venda);
                $item->dias_expiracao = $request->dias_expiracao;
                $item->mensagem_padrao_whatsapp = $request->mensagem_padrao_whatsapp ?? '';
                $item->mensagem_automatica_5_dias = $request->mensagem_automatica_5_dias ?? '';
                $item->mensagem_automatica_1_dia = $request->mensagem_automatica_1_dia ?? '';
                $item->save();
                session()->flash('flash_success', 'Configuração atualizada!');
            }else{
                $request->merge([
                    'valor_percentual' => __convert_value_bd($request->valor_percentual),
                    'valor_minimo_venda' => __convert_value_bd($request->valor_minimo_venda),
                    'percentual_maximo_venda' => __convert_value_bd($request->percentual_maximo_venda),
                    'mensagem_padrao_whatsapp' => $request->mensagem_padrao_whatsapp ?? '',
                    'mensagem_automatica_5_dias' => $request->mensagem_automatica_5_dias ?? '',
                    'mensagem_automatica_1_dia' => $request->mensagem_automatica_1_dia ?? '',
                ]);

                CashBackConfig::create($request->all());
                session()->flash('flash_success', 'Configuração criada!');
            }
        }catch(\Exception $e){
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }

        return redirect()->back();

    }

    private function _validate(Request $request){
        $rules = [
            'valor_percentual' => 'required',
            'dias_expiracao' => 'required',
            'valor_minimo_venda' => 'required',
            'percentual_maximo_venda' => 'required',
        ];

        $messages = [
            'valor_percentual.required' => 'Campo obrigatório.',
            'dias_expiracao.required' => 'Campo obrigatório.',
            'valor_minimo_venda.required' => 'Campo obrigatório.',
            'percentual_maximo_venda.required' => 'Campo obrigatório.',
        ];
        $this->validate($request, $rules, $messages);
    }
}
