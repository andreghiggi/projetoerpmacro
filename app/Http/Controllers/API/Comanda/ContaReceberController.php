<?php

namespace App\Http\Controllers\API\Comanda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContaReceber;
use App\Models\CategoriaConta;
use App\Models\Localizacao;

class ContaReceberController extends Controller
{
    public function index(Request $request){
        $data = ContaReceber::where('empresa_id', $request->empresa_id)
        ->select('id', 'cliente_id', 'descricao', 'valor_integral', 'valor_recebido', 'status', 'data_vencimento', 'data_recebimento', 
            'observacao', 'tipo_pagamento', 'categoria_conta_id', 'created_at')
        ->with(['cliente', 'categoria'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($data, 200);
    }

    public function categorias(Request $request){
        $data = CategoriaConta::where('empresa_id', $request->empresa_id)
        ->select('id', 'nome')
        ->orderBy('nome', 'asc')
        ->where('status', 1)
        ->where('tipo', 'receber')
        ->get();
        
        return response()->json($data, 200);
    }

    public function store(Request $request){
        try {

            $local = Localizacao::where('empresa_id', $request->empresa_id)
            ->where('status', 1)->first();

            $request->merge([
                'valor_integral' => $request->valor,
                'valor_original' => $request->valor,
                'valor_recebido' => $request->status ? $request->valor : 0,
                'data_recebimento' => $request->status ? $request->data_vencimento : null,
                'local_id' => $local->id
            ]);

            $conta = ContaReceber::create($request->all());
            return response()->json($conta, 200);

        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Conta a Receber', 'erro', $e->getMessage());
            return response()->json($e->getMessage(), 401);
        }
    }

    public function update(Request $request, $id){
        try {
            $item = ContaReceber::findOrFail($id);

            $request->merge([
                'valor_integral' => $request->valor,
                'valor_original' => $request->valor,
                'valor_recebido' => $request->status ? $request->valor : 0,
                'data_recebimento' => $request->status ? $request->data_vencimento : null,
            ]);

            $item->fill($request->all())->save();
            return response()->json($item, 200);

        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Conta a Receber', 'erro', $e->getMessage());
            return response()->json($e->getMessage(), 401);
        }
    }

    public function receive(Request $request){
        try {
            $item = ContaReceber::findOrFail($request->conta_id);

            $item->status = 1;
            $item->valor_recebido = $request->valor;
            $item->data_recebimento = $request->data_recebimento;
            $item->tipo_pagamento = $request->tipo_pagamento;

            $item->save();
            return response()->json($item, 200);

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function destroy($id){
        $item = ContaReceber::findOrFail($id);

        try{
            $item->delete();
            return response()->json("ok", 200);

        }catch(\Exception $e){
            __createLog($item->empresa_id, 'Conta a Receber', 'erro', $e->getMessage());
            return response()->json($e->getMessage(), 401);
        }
    }


}
