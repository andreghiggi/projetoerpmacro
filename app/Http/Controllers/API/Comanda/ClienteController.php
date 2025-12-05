<?php

namespace App\Http\Controllers\API\Comanda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\ContaReceber;
use App\Models\Cidade;
use App\Models\Empresa;

class ClienteController extends Controller
{
    public function findCliente(Request $request){
        $mask = '(##) #####-####';
        $telefone = preg_replace('/[^0-9]/', '', $request->telefone);

        $telefone = __mask($telefone, $mask);
        $item = Cliente::where('telefone', $telefone)
        ->where('empresa_id', $request->empresa_id)->first();
        
        return response()->json($item, 200);
    }

    public function index(Request $request){
        $data = Cliente::where('empresa_id', $request->empresa_id)
        ->select('id', 'razao_social', 'cpf_cnpj', 'ie', 'rua', 'numero', 'bairro', 'telefone', 'email', 'complemento', 'cep', 'cidade_id',
            'limite_credito', 'status', 'lista_preco_id', 'created_at')
        ->with('cidade')
        ->orderBy('razao_social', 'desc')
        ->where('status', 1)
        ->get();

        foreach($data as $c){
            $c->soma_contas = ContaReceber::where('cliente_id', $c->id)
            ->where('status', 0)->sum('valor_integral');
        }

        return response()->json($data, 200);
    }

    public function cidades(Request $request){
        $cidades = Cidade::select('id', 'nome', 'uf', 'codigo')
        ->get();

        $empresa = Empresa::findOrFail($request->empresa_id);
        $data = [
            'cidades' => $cidades,
            'cidade_padrao' => $empresa->cidade_id
        ];
        return response()->json($data, 200);
    }

    public function cidades2(Request $request){
        $data = Cidade::select('id', 'nome', 'uf', 'codigo')
        ->get();

        return response()->json($data, 200);
    }

    public function store(Request $request){
        try{
            Cliente::create($request->all());
            return response()->json("ok", 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

    public function update(Request $request, $id){
        try{
            $item = Cliente::findOrFail($id);
            $item->fill($request->all())->save();
            return response()->json("ok", 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

    public function destroy($id){
        try{
            $item = Cliente::findOrFail($id);
            $item->tributacao()->delete();
            $item->enderecosEcommerce()->delete();

            foreach($item->carrinhosDelivery as $c){
                $c->itens()->delete();
                $c->delete();
            }

            $item->delete();
            return response()->json("ok", 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

}
