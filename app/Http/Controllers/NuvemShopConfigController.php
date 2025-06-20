<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NuvemShopConfig;
use App\Utils\NuvemShopUtil;

class NuvemShopConfigController extends Controller
{

    protected $util;

    public function __construct(NuvemShopUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request){
        $item = NuvemShopConfig::where('empresa_id', $request->empresa_id)
        ->first();

        return view('nuvem_shop_config.index', compact('item'));
    }

    public function store(Request $request){
        $item = NuvemShopConfig::where('empresa_id', $request->empresa_id)
        ->first();

        if($item == null){
            NuvemShopConfig::create($request->all());
            session()->flash("flash_success", "Configuração criada com sucesso!");
        }else{
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada com sucesso!");
        }
        return redirect()->back();
    }
}
