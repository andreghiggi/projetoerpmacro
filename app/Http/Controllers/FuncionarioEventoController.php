<?php

namespace App\Http\Controllers;

use App\Models\EventoSalario;
use App\Models\Funcionario;
use App\Models\FuncionarioEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuncionarioEventoController extends Controller
{
    public function index(Request $request)
    {
        $data = Funcionario::select('funcionarios.*')
        ->join('funcionario_eventos', 'funcionario_eventos.funcionario_id', '=', 'funcionarios.id')
        ->where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where(function ($quer) use ($request) {
                return $quer->where('nome', 'LIKE', "%$request->nome%");
            });
        })
        ->groupBy('funcionarios.id')
        ->paginate(__itensPagina());
        return view('funcionario_evento.index', compact('data'));
    }

    public function create()
    {
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        $eventos = EventoSalario::where('empresa_id', request()->empresa_id)->get();
        return view('funcionario_evento.create', compact('funcionarios', 'eventos'));
    }

    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                for ($i = 0; $i < sizeof($request->evento); $i++) {
                    $item = [
                        'evento_id' => $request->evento[$i],
                        'funcionario_id' => $request->funcionario_id,
                        'condicao' => $request->condicao[$i],
                        'metodo' => $request->metodo[$i],
                        'valor' => __convert_value_bd($request->valor[$i]),
                        'ativo' => $request->ativo[$i]
                    ];
                    FuncionarioEvento::create($item);
                }
            });
            session()->flash("flash_success", "Cadastrado com Sucesso");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Não foi possivel fazer o cadastro" . $e->getMessage());
        }
        return redirect()->route('funcionario-eventos.index');
    }

    public function edit($id)
    {
        $item = Funcionario::findOrFail($id);
        $eventos = EventoSalario::where('empresa_id', request()->empresa_id)->get();
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        return view('funcionario_evento.edit', compact('item', 'eventos', 'funcionarios'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                FuncionarioEvento::where('funcionario_id', $id)->delete();
                for ($i = 0; $i < sizeof($request->evento); $i++) {
                    $item = [
                        'evento_id' => $request->evento[$i],
                        'funcionario_id' => $id,
                        'condicao' => $request->condicao[$i],
                        'metodo' => $request->metodo[$i],
                        'valor' => __convert_value_bd($request->valor[$i]),
                        'ativo' => $request->ativo[$i]
                    ];
                    FuncionarioEvento::create($item);
                }
            });
            session()->flash("flash_success", "Eventos atualizados!");
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('funcionario-eventos.index');
    }

    public function destroy($id)
    {
        try {
            FuncionarioEvento::where('funcionario_id', $id)->delete();
            session()->flash("flash_success", "Eventos removido!");
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('funcionario-eventos.index');
    }
}