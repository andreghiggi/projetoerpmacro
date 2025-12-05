<?php

namespace App\Http\Controllers\API\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Caixa;
use App\Models\Localizacao;

class CaixaController extends Controller
{
    protected $prefix = 'caixa';

    public function open(Request $request){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $data = Caixa::where('empresa_id', $request->empresa_id)
        ->where('usuario_id', $request->usuario_id)
        ->where('status', 1)
        ->first();

        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);
        return response()->json($data, 200);
    }

    public function store(Request $request){

        if(!__validaPermissaoToken($request->token, $this->prefix.".create")){
            return response()->json("Permissão negada!", 403);
        }
        try{
            $localizacao = Localizacao::where('empresa_id', $request->empresa_id)->first();

            $item = Caixa::where('empresa_id', $request->empresa_id)
            ->where('usuario_id', $request->usuario_id)
            ->where('status', 1)
            ->first();

            if($item != null){
                return response()->json("Caixa já esta aberto!", 403);
            }

            $data = [
                'empresa_id' => $request->empresa_id,
                'usuario_id' => $request->usuario_id,
                'valor_abertura' => $request->valor ? __convert_value_bd($request->valor) : 0,
                'observacao' => $request->observacao ?? "",
                'local_id' => $localizacao->id,
                'status' => 1,
                'valor_fechamento' => 0,
            ];
            $item = Caixa::create($data);
            __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'create', $this->prefix);
            return response()->json($item, 200);
        }catch(\Exception $e){
            __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'create', $this->prefix);
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    public function close(Request $request){

        if(!__validaPermissaoToken($request->token, $this->prefix.".create")){
            return response()->json("Permissão negada!", 403);
        }

        try{
            $localizacao = Localizacao::where('empresa_id', $request->empresa_id)->first();

            $item = Caixa::where('empresa_id', $request->empresa_id)
            ->where('usuario_id', $request->usuario_id)
            ->where('status', 1)
            ->first();

            if($item == null){
                return response()->json("Nenhum caixa encontrado!", 403);
            }

            $item->status = 0;
            $item->data_fechamento = date('Y-m-d h:i:s');
            $item->save();
            
            __createApiLog($request->empresa_id, $request->token, 'sucesso', 'Caixa fechado', 'create', $this->prefix);
            return response()->json($item, 200);
        }catch(\Exception $e){
            __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'create', $this->prefix);
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }
}
