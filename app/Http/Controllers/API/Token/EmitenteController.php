<?php

namespace App\Http\Controllers\API\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Cidade;

class EmitenteController extends Controller
{
    protected $prefix = 'emitente';
    
    public function index(Request $request){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $item = Empresa::findOrFail($request->empresa_id);
        $item->setHidden(['arquivo', 'token', 'token_nfse', 'senha', 'info', 'empresa_selecionada', 'percentual_comissao', 
            'tipo_contador', 'logo', 'natureza_id_pdv', 'arquivo']);
        return response()->json($item, 200);
    }

    public function update(Request $request){

        try{

            $item = Empresa::find($request->empresa_id);
            if($item == null){
                return response()->json("Empresa não encontrado!", 403);
            }

            if($request->cidade){
                $cidade = Cidade::where('nome', $request->cidade)
                ->where('uf', $request->uf)->first();

                if($cidade == null){
                    return response()->json("Cidade não encontrada!", 403);
                }

                $request->merge([
                    'cidade_id' => $cidade->id
                ]);
            }

            $item->fill($request->all())->save();
            $item->setHidden(['arquivo', 'token', 'token_nfse', 'senha', 'info', 'empresa_selecionada', 'percentual_comissao', 
                'tipo_contador', 'logo', 'natureza_id_pdv']);
            return response()->json($item, 200);
        }catch(\Exception $e){
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }
}
