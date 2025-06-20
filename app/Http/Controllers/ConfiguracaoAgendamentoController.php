<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfiguracaoAgendamento;

class ConfiguracaoAgendamentoController extends Controller
{
    public function index(Request $request)
    {
        $item = ConfiguracaoAgendamento::where('empresa_id', $request->empresa_id)
        ->first();

        return view('config_agendamento.index', compact('item'));
    }

    public function store(Request $request)
    {
        $item = ConfiguracaoAgendamento::where('empresa_id', $request->empresa_id)
        ->first();

        $request->merge([
            'mensagem_manha' => $request->mensagem_manha ?? '',
            'mensagem_alerta' => $request->mensagem_alerta ?? '',
        ]);

        if ($item != null) {
         
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada!");
        } else {
            
            ConfiguracaoAgendamento::create($request->all());
            session()->flash("flash_success", "Configuração cadastrada!");
        }
        return redirect()->back();
    }
}
