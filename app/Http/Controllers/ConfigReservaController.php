<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReservaConfig;
use App\Models\Empresa;

class ConfigReservaController extends Controller
{
    public function index(Request $request)
    {

        $empresa = Empresa::findOrFail($request->empresa_id);
        $cpfCnpj = $empresa->cpf_cnpj;

        $item = ReservaConfig::where('empresa_id', $request->empresa_id)
        ->first();
        if($item != null){
            $cpfCnpj = $item->cpf_cnpj;
        }

        return view('reserva_config.index', compact('item', 'cpfCnpj'));
    }

    public function store(Request $request)
    {

        $item = ReservaConfig::where('empresa_id', $request->empresa_id)
        ->first();

        if ($item != null) {
            //update

            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada!");
        } else {
            
            ReservaConfig::create($request->all());
            session()->flash("flash_success", "Configuração cadastrada!");
        }
        return redirect()->back();
    }
}
