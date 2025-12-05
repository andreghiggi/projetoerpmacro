<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendiZapConfig;

class VendiZapConfigController extends Controller
{
    public function index(Request $request){
        $item = VendiZapConfig::where('empresa_id', $request->empresa_id)
        ->first();

        return view('vendizap_config.index', compact('item'));
    }

    public function store(Request $request){
        $item = VendiZapConfig::where('empresa_id', $request->empresa_id)
        ->first();

        if($item == null){
            VendiZapConfig::create($request->all());
            session()->flash("flash_success", "Configuração criada com sucesso!");
        }else{
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada com sucesso!");
        }
        return redirect()->back();
    }
}
