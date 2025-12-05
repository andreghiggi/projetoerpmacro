<?php

namespace App\Http\Controllers\API\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContaPagar;
use App\Models\CategoriaConta;
use App\Models\Localizacao;

class ContaPagarController extends Controller
{
    public function index(Request $request){
        $data = ContaPagar::where('empresa_id', $request->empresa_id)
        ->select('id', 'fornecedor_id', 'descricao', 'valor_integral', 'valor_pago', 'status', 'data_vencimento', 'data_pagamento', 
            'observacao', 'tipo_pagamento', 'categoria_conta_id', 'created_at')
        ->with(['fornecedor', 'categoria'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($data, 200);
    }

    public function categorias(Request $request){
        $data = CategoriaConta::where('empresa_id', $request->empresa_id)
        ->select('id', 'nome')
        ->orderBy('nome', 'asc')
        ->where('status', 1)
        ->where('tipo', 'pagar')
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
                'valor_pago' => $request->status ? $request->valor : 0,
                'data_pagamento' => $request->status ? $request->data_vencimento : null,
                'local_id' => $local->id
            ]);

            $conta = ContaPagar::create($request->all());
            return response()->json($conta, 200);

        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Conta a Pagar', 'erro', $e->getMessage());
            return response()->json($e->getMessage(), 401);
        }
    }

    public function update(Request $request, $id){
        try {
            $item = ContaPagar::findOrFail($id);

            $request->merge([
                'valor_integral' => $request->valor,
                'valor_original' => $request->valor,
                'valor_pago' => $request->status ? $request->valor : 0,
                'data_pagamento' => $request->status ? $request->data_vencimento : null,
            ]);

            $item->fill($request->all())->save();
            return response()->json($item, 200);

        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Conta a Pagar', 'erro', $e->getMessage());
            return response()->json($e->getMessage(), 401);
        }
    }

    public function pay(Request $request){
        try {
            $item = ContaPagar::findOrFail($request->conta_id);

            $item->status = 1;
            $item->valor_pago = $request->valor;
            $item->data_pagamento = $request->data_recebimento;
            $item->tipo_pagamento = $request->tipo_pagamento;

            $item->save();
            return response()->json($item, 200);

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function destroy($id){
        $item = ContaPagar::findOrFail($id);

        try{
            $item->delete();
            return response()->json("ok", 200);

        }catch(\Exception $e){
            __createLog($item->empresa_id, 'Conta a Pagar', 'erro', $e->getMessage());
            return response()->json($e->getMessage(), 401);
        }
    }

}
