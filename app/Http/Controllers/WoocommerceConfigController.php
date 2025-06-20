<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WoocommerceConfig;

class WoocommerceConfigController extends Controller
{
    
    public function index(Request $request){
        $item = WoocommerceConfig::where('empresa_id', $request->empresa_id)
        ->first();

        return view('woocommerce_config.index', compact('item'));
    }

    public function store(Request $request){
        $item = WoocommerceConfig::where('empresa_id', $request->empresa_id)
        ->first();

        if($item == null){
            WoocommerceConfig::create($request->all());
            session()->flash("flash_success", "Configuração criada com sucesso!");
        }else{
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada com sucesso!");
        }
        return redirect()->back();
    }

}
