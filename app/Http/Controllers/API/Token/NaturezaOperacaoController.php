<?php

namespace App\Http\Controllers\API\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NaturezaOperacao;

class NaturezaOperacaoController extends Controller
{
    protected $prefix = 'natureza_operacao';

    public function index(Request $request){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $data = NaturezaOperacao::where('empresa_id', $request->empresa_id)
        ->get()
        ->each(function($row)
        {
            $row->setHidden(['_id_import']);
        });
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);
        return response()->json($data, 200);
    }

    public function find(Request $request, $id){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $empresa_id = $request->empresa_id;
        $item = NaturezaOperacao::where('empresa_id', $empresa_id)->findOrFail($id);

        $item->setHidden(['_id_import']);
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);

        return response()->json($item, 200);
    }

    public function store(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".create")){
            return response()->json("Permissão negada!", 403);
        }
        try{
            if($request->padrao == 1){
            NaturezaOperacao::where('empresa_id', $request->empresa_id)
            ->update(['padrao' => 0]);
        }
            // $request->merge([
            //     'descricao' => $request->descricao,
            //     'padrao' => $request->padrao ?? 0,
            //     'empresa_id' => $request->empresa_id,
            // ]);
            $item = NaturezaOperacao::create($request->all());
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
            if($request->padrao == 1){
                NaturezaOperacao::where('empresa_id', $request->empresa_id)
                ->update(['padrao' => 0]);
            }

            $item = NaturezaOperacao::where('empresa_id', $request->empresa_id)->findOrFail($request->id);
            if($item == null){
                return response()->json("Natureza de operação não encontrada!", 403);
            }
            $item->fill($request->all())->save();

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

            $item = NaturezaOperacao::where('empresa_id', $request->empresa_id)->find($request->id);
            if($item == null){
                return response()->json("Natureza de operação não encontrada!", 403);
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
