<?php

namespace App\Http\Controllers;

use App\Models\ConfigGeral;
use Illuminate\Http\Request;

class ConfigGeralController extends Controller
{
    public function create()
    {
        $item = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        if($item != null){
            $item->notificacoes = json_decode($item->notificacoes);
            $item->tipos_pagamento_pdv = $item != null && $item->tipos_pagamento_pdv ? json_decode($item->tipos_pagamento_pdv) : [];
            $item->acessos_pdv_off = $item != null && $item->acessos_pdv_off ? json_decode($item->acessos_pdv_off) : [];
        }

        return view('config_geral.index', compact('item'));
    }

    public function store(Request $request)
    {
        $item = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        try {

            if(!isset($request->notificacoes)){
                $request->merge([
                    'notificacoes' => '[]'
                ]);
            }else{
                $request->merge([
                    'notificacoes' => json_encode($request->notificacoes)
                ]);
            }

            if(!isset($request->tipos_pagamento_pdv)){
                $request->merge([
                    'tipos_pagamento_pdv' => '[]'
                ]);
            }else{
                $request->merge([
                    'tipos_pagamento_pdv' => json_encode($request->tipos_pagamento_pdv)
                ]);
            }

            if(!isset($request->acessos_pdv_off)){
                $request->merge([
                    'acessos_pdv_off' => '[]'
                ]);
            }else{
                $request->merge([
                    'acessos_pdv_off' => json_encode($request->acessos_pdv_off)
                ]);
            }

            $request->merge([
                'margem_combo' => $request->margem_combo ? __convert_value_bd($request->margem_combo) : 50,
                'percentual_lucro_produto' => $request->percentual_lucro_produto ?? 0
            ]);

            if ($item == null) {
                ConfigGeral::create($request->all());
                session()->flash("flash_success", "Dados cadastrados com sucesso!");
            } else {
                $item->fill($request->all())->save();
                session()->flash("flash_success", "Dados alterados com sucesso!");
            }
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }
}
