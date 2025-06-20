<?php

namespace App\Http\Controllers\API\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaProduto;

class CategoriaProdutoController extends Controller
{

    protected $prefix = 'categoria_produtos';

    public function index(Request $request){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $data = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->select('nome', 'id', 'categoria_id')->where('status', 1)
        ->get();
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);
        return response()->json($data, 200);
    }

    public function find(Request $request, $id){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $empresa_id = $request->empresa_id;
        $item = CategoriaProduto::where('empresa_id', $empresa_id)
        ->select('nome', 'id', 'categoria_id')->findOrFail($id);
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);

        return response()->json($item, 200);
    }

    public function store(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".create")){
            return response()->json("Permissão negada!", 403);
        }
        try{

            $item = CategoriaProduto::create([
                'nome' => $request->nome,
                'categoria_id' => $request->categoria_id ?? null,
                'empresa_id' => $request->empresa_id
            ]);

            __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'create', $this->prefix);
            return response()->json($item, 200);

        }catch(\Exception $e){
            __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'create', $this->prefix);
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    public function update(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".update")){
            return response()->json("Permissão negada!", 403);
        }
        try{

            $item = CategoriaProduto::where('empresa_id', $request->empresa_id)
            ->select('nome', 'id', 'categoria_id')->findOrFail($request->id);
            if($item == null){
                return response()->json("Categoria não encontrada!", 403);
            }
            $item->nome = $request->nome;
            $item->categoria_id = $request->categoria_id ?? null;
            $item->save();
            __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'update', $this->prefix);
            return response()->json($item, 200);

        }catch(\Exception $e){
            __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'update', $this->prefix);
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    public function delete(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".delete")){
            return response()->json("Permissão negada!", 403);
        }
        try{

            $item = CategoriaProduto::where('empresa_id', $request->empresa_id)
            ->select('nome', 'id', 'categoria_id')->find($request->id);
            if($item == null){
                return response()->json("Categoria não encontrada!", 403);
            }
            $item->delete();
            __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'delete', $this->prefix);
            return response()->json($item, 200);

        }catch(\Exception $e){
            __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'delete', $this->prefix);
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }
}
