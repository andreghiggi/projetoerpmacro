<?php

namespace App\Http\Controllers;

use App\Models\ConectaVendaConfig;
use Illuminate\Http\Request;

class ConectaVendaConfigController extends Controller
{
    protected $util;

    public function __construct(ConectaVendaConfig $util)
    {
        $this->util = $util;
    }

    public function index(Request $request){
        $item = ConectaVendaConfig::where('empresa_id', $request->empresa_id)
            ->first();

        return view('conecta_venda_config.index', compact('item'));
    }

    public function store(Request $request){
        $item = ConectaVendaConfig::where('empresa_id', $request->empresa_id)
            ->first();

        if($item == null){
            ConectaVendaConfig::create($request->all());
            session()->flash("flash_success", "Configuração criada com sucesso!");
        }else{
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada com sucesso!");
        }
        return redirect()->back();
    }
}
