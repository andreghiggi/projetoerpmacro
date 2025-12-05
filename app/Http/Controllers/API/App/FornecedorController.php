<?php

namespace App\Http\Controllers\API\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fornecedor;
use App\Models\ContaPagar;

class FornecedorController extends Controller
{
    public function index(Request $request){
        $data = Fornecedor::where('empresa_id', $request->empresa_id)
        ->select('id', 'razao_social', 'cpf_cnpj', 'ie', 'rua', 'numero', 'bairro', 'telefone', 'email', 'complemento', 'cep', 'cidade_id', 
            'created_at')
        ->with('cidade')
        ->orderBy('razao_social', 'desc')
        ->get();

        foreach($data as $c){
            $c->soma_contas = ContaPagar::where('fornecedor_id', $c->id)
            ->where('status', 0)->sum('valor_integral');
        }

        return response()->json($data, 200);
    }

    public function store(Request $request){
        try{
            Fornecedor::create($request->all());
            return response()->json("ok", 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

    public function update(Request $request, $id){
        try{
            $item = Fornecedor::findOrFail($id);
            $item->fill($request->all())->save();
            return response()->json("ok", 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

    public function destroy($id){
        try{
            $item = Fornecedor::findOrFail($id);
            
            if(sizeof($item->compras) > 0){
                return response()->json("Não é possível remover um fornecedor com compras!", 401);
            }

            $item->produtoFornecedor()->delete();
            $item->delete();

            return response()->json("ok", 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

}
