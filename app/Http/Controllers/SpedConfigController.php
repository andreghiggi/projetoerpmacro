<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpedConfig;

class SpedConfigController extends Controller
{
    public function index(Request $request){
        $item = SpedConfig::where('empresa_id', $request->empresa_id)
        ->first();

        return view('sped_config.index', compact('item'));
    }

    public function store(Request $request){
        $item = SpedConfig::where('empresa_id', $request->empresa_id)->first();
        $request->merge([
            'data_vencimento' => $request->data_vencimento ? $request->data_vencimento : '10'
        ]);
        try{
            if($item == null){
                SpedConfig::create($request->all());
                session()->flash("flash_success", "Configuração criada com sucesso");
            }else{
                $item->fill($request->all())->save();
                session()->flash("flash_success", "Configuração atualizada com sucesso");

            }
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado " . $e->getMessage());
        }
        return redirect()->back();
    }
}
