<?php

namespace App\Http\Controllers;

use App\Models\ApuracaoMensal;
use App\Models\ApuracaoMensalEvento;
use App\Models\ContaPagar;
use App\Models\EventoSalario;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApuracaoMensalController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:apuracao_mensal_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:apuracao_mensal_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:apuracao_mensal_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:apuracao_mensal_delete', ['only' => ['destroy']]);
    }
    
    public function index(Request $request)
    {
        $funcionario_id = $request->funcionario_id;
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $data = ApuracaoMensal::select('apuracao_mensals.*')
        ->join('funcionarios', 'apuracao_mensals.funcionario_id', '=', 'funcionarios.id')
        ->where('empresa_id', request()->empresa_id)
        ->when(!empty($funcionario_id), function ($query) use ($funcionario_id) {
            return $query->where('funcionarios.id', $funcionario_id);
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('apuracao_mensals.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('apuracao_mensals.created_at', '<=', $end_date);
        })
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

        $funcionario = null;
        if($funcionario_id){
            $funcionario = Funcionario::findOrFail($funcionario_id);
        }

        return view('apuracao_mensal.index', compact('data', 'funcionario'));
    }

    public function create()
    {
        $funcionarios = Funcionario::orderBy('nome')
        ->where('empresa_id', request()->empresa_id)
        ->get();
        $mesAtual = (int)date('m') - 1;
        return view('apuracao_mensal.create', compact('mesAtual', 'funcionarios'));
    }

    public function getEventos($id)
    {
        try {
            $item = Funcionario::findOrFail($id);
            if (sizeof($item->eventos) == 0) {
                return response()->json("", 200);
            }
            return view('apuracao_mensal.eventos', compact('item'));
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function store(Request $request)
    {
        try {
            // dd($request->all());
            $ap = DB::transaction(function () use ($request) {
                $ap = [
                    'funcionario_id' => $request->funcionario_id,
                    'mes' => $request->mes,
                    'ano' => $request->ano,
                    'valor_final' => __convert_value_bd($request->valor_total),
                    'forma_pagamento' => $request->tipo_pagamento,
                    'observacao' => $request->observacao ?? ''
                ];
                $result = ApuracaoMensal::create($ap);
                for ($i = 0; $i < sizeof($request->evento); $i++) {
                    $ev = EventoSalario::find($request->evento[$i]);
                    if ($ev) {
                        ApuracaoMensalEvento::create([
                            'apuracao_id' => $result->id,
                            'evento_id' => $ev->id,
                            'valor' => __convert_value_bd($request->valor[$i]),
                            'metodo' => $request->metodo[$i],
                            'condicao' => $request->condicao[$i],
                            'nome' => $ev->nome
                        ]);
                    }
                }
                return $result;
            });
            __createLog($request->empresa_id, 'Apuração Mensal', 'cadastrar', $ap->funcionario->nome . " - $ap->mes/$ap->ano");
            session()->flash("flash_success", "Apuração criada!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Apuração Mensal', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('apuracao-mensal.index');
    }

    public function contaPagar($id)
    {
        $item = ApuracaoMensal::findOrFail($id);

        return view('apuracao_mensal.conta_pagar', compact('item'));
    }

    public function setConta(Request $request, $id)
    {
        try {
            $item = ApuracaoMensal::findOrFail($id);

            $local_id = null;
            $caixa = __isCaixaAberto();
            if($caixa != null){
                $local_id = $caixa->local_id;
            }else{
                $local_id = __getLocalAtivo()->id;
            }
            
            $conta = [
                'compra_id' => null,
                'data_vencimento' => $request->data_vencimento,
                'valor_integral' => str_replace(",", ".", $request->valor_integral),
                'valor_pago' => $request->status ? __convert_value_bd($request->valor_pago) : 0,
                'status' => $request->status,
                'descricao' => $request->descricao,
                'tipo_pagamento' => $request->tipo_pagamento ?? '',
                'fornecedor_id' => null,
                'empresa_id' => request()->empresa_id,
                'local_id' => $local_id
            ];
            $result = ContaPagar::create($conta);

            $item->conta_pagar_id = $result->id;
            $item->save();
            session()->flash("flash_success", "Adicionado em contas a pagar!");
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('apuracao-mensal.index');
    }

    public function show($id)
    {
        $item = ApuracaoMensal::findOrFail($id);

        return view('apuracao_mensal.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = ApuracaoMensal::findOrFail($id);
        try {
            $descricaoLog = $item->funcionario->nome . " - $item->mes/$item->ano";

            if($item->contaPagar){
                $item->contaPagar->delete();
            }
            $item->eventos()->delete();
            $item->delete();
            __createLog(request()->empresa_id, 'Apuração Mensal', 'excluir', $descricaoLog);

            session()->flash("flash_success", "Registro removido!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Apuração Mensal', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }
}