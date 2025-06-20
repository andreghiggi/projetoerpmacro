<?php

namespace App\Http\Controllers\API\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cotacao;
use App\Models\ItemCotacao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContacaoController extends Controller
{
    protected $prefix = 'cotacao';
    
    public function index(Request $request){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $data = Cotacao::where('empresa_id', $request->empresa_id)
        ->with(['itens', 'fatura'])
        ->get()
        ->each(function($row)
        {
            $row->setHidden(['hash_link']);
        });
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);
        return response()->json($data, 200);
    }

    public function find(Request $request, $id){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $empresa_id = $request->empresa_id;
        $item = Cotacao::where('empresa_id', $empresa_id)
        ->with(['itens', 'fatura'])->findOrFail($id);

        $item->setHidden(['hash_link']);
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);

        return response()->json($item, 200);
    }

    public function store(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".create")){
            return response()->json("Permissão negada!", 403);
        }
        try{
            $validaRequest = $this->validaRequest($request);
            if(sizeof($validaRequest) > 0){
                return response()->json($validaRequest, 403);
            }
            foreach($request->fornecedores as $f){

                $cotacao = DB::transaction(function () use ($request, $f) {
                    $cotacao = Cotacao::create([
                        'empresa_id' => $request->empresa_id,
                        'fornecedor_id' => $f,
                        'hash_link' => Str::random(30),
                        'referencia' => Str::random(7),
                        'observacao' => $request->observacao ?? '',
                        'estado' => $request->estado
                    ]);

                    foreach($request->itens as $key => $i){
                        ItemCotacao::create([
                            'cotacao_id' => $cotacao->id,
                            'quantidade' => __convert_value_bd($i['quantidade']),
                            'produto_id' => $i['produto_id'],
                        ]);
                    }

                    return $cotacao;
                });
            }

            $item = Cotacao::with(['itens'])->findOrFail($cotacao->id);
            $item->setHidden(['hash_link']);

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
            $empresa_id = $request->empresa_id;

            $item = Cotacao::where('empresa_id', $empresa_id)
            ->findOrFail($request->id);

            if($item->empresa_id !=  $empresa_id){
                return response()->json("Não permitido!", 403);
            }

            DB::transaction(function () use ($request, $item) {

                $data = [
                    'empresa_id' => $request->empresa_id,
                    'fornecedor_id' => $request->fornecedor_id,
                    'observacao' => $request->observacao ?? '',
                    'estado' => $request->estado
                ];
                $item->update($data);
                if(isset($request->itens)){
                    $validaRequest = $this->validaRequestItens($request);
                    $item->itens()->delete();

                    foreach($request->itens as $key => $i){
                        ItemCotacao::create([
                            'cotacao_id' => $item->id,
                            'quantidade' => __convert_value_bd($i['quantidade']),
                            'produto_id' => $i['produto_id'],
                        ]);
                    }
                }
            });
            
            $item = Cotacao::with(['itens'])->findOrFail($item->id);
            $item->setHidden(['hash_link']);

            __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'update', $this->prefix);
            return response()->json($item, 200);

        }catch(\Exception $e){
            __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'update', $this->prefix);
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    private function validaRequest($request){
        $dataMessage = [];

        if(!isset($request->fornecedores) || sizeof($request->fornecedores) <= 0){
            array_push($dataMessage, "Informe o fornecedor da cotação");
        }

        if(!isset($request->estado) || $request->estado <= 0){
            array_push($dataMessage, "Informe o estado da cotação, ['nova', 'respondida', 'aprovada', 'rejeitada']");
        }
        foreach($request->itens as $key => $i){
            if(!isset($i['produto_id'])){
                array_push($dataMessage, "Informe o produto_id do item " . $key+1);
            }
            if(!isset($i['quantidade'])){
                array_push($dataMessage, "Informe a quantidade do item " . $key+1);
            }
        }
        return $dataMessage;
    }

    private function validaRequestItens($request){
        $dataMessage = [];

        foreach($request->itens as $key => $i){
            if(!isset($i['produto_id'])){
                array_push($dataMessage, "Informe o produto_id do item " . $key+1);
            }
            if(!isset($i['quantidade'])){
                array_push($dataMessage, "Informe a quantidade do item " . $key+1);
            }
        }
        return $dataMessage;
    }

    public function delete(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".delete")){
            return response()->json("Permissão negada!", 403);
        }
        try{
            $empresa_id = $request->empresa_id;

            $item = Cotacao::where('empresa_id', $empresa_id)
            ->with(['itens', 'fatura'])->find($request->id);
            if($item == null){
                return response()->json("Cotação não encontrada!", 403);
            }

            if($item->empresa_id !=  $empresa_id){
                return response()->json("Não permitido!", 403);
            }

            $item->itens()->delete();
            $item->fatura()->delete();
            $item->delete();
            __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'delete', $this->prefix);
            return response()->json($item, 200);

        }catch(\Exception $e){
            __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'delete', $this->prefix);
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }
}
