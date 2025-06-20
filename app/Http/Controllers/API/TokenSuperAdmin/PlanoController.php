<?php

namespace App\Http\Controllers\API\TokenSuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plano;

class PlanoController extends Controller
{

    public function index(Request $request){

        $data = Plano::all();
        return response()->json($data, 200);
    }

    public function find(Request $request, $id){

        $item = Plano::find($id);
        if($item == null){
            return response()->json("Plano não encontrado!", 403);
        }
        return response()->json($item, 200);
    }

    public function store(Request $request){

        try{
            $validaRequest = $this->validaRequest($request);
            if(sizeof($validaRequest) > 0){
                return response()->json($validaRequest, 403);
            }

            if(!isset($request->modulos)){
                $request->merge([
                    'modulos' => '[]'
                ]);
            }else{
                $request->merge([
                    'modulos' => json_encode($request->modulos)
                ]);
            }

            $request->merge([
                'nome' => $request->nome,
                'valor' => __convert_value_bd($request->valor),
                'auto_cadastro' => $request->auto_cadastro ?? 0,
                'fiscal' => $request->fiscal ?? 1,
                'imagem' => '',
                'descricao' => $request->descricao ?? '',
            ]);

            $item = Plano::create($request->all());

            return response()->json($item, 200);

        }catch(\Exception $e){
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    public function update(Request $request){
        
        try{

            $item = Plano::find($request->id);
            if($item == null){
                return response()->json("Plano não encontrado!", 403);
            }
            $item->fill($request->all())->save();
            return response()->json($item, 200);

        }catch(\Exception $e){
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    private function validaRequest($request){
        $dataMessage = [];


        if(!isset($request->nome) || $request->nome == ""){
            array_push($dataMessage, "Informe o nome");
        }
        if(!isset($request->valor) || $request->valor <= 0){
            array_push($dataMessage, "Informe a valor");
        }
        if(!isset($request->maximo_nfes)){
            array_push($dataMessage, "Informe o limite de emissão NFe, variável maximo_nfes");
        }
        if(!isset($request->maximo_nfces)){
            array_push($dataMessage, "Informe o limite de emissão NFCe, variável maximo_nfces");
        }
        if(!isset($request->maximo_ctes)){
            array_push($dataMessage, "Informe o limite de emissão CTe, variável maximo_ctes");
        }
        if(!isset($request->maximo_mdfes)){
            array_push($dataMessage, "Informe o limite de emissão MDFe, variável maximo_mdfes");
        }
        if(!isset($request->maximo_usuarios)){
            array_push($dataMessage, "Informe o limite de Usuários, variável maximo_usuarios");
        }
        if(!isset($request->maximo_locais)){
            array_push($dataMessage, "Informe o limite de Localizações, variável maximo_locais");
        }
        if(!isset($request->intervalo_dias)){
            array_push($dataMessage, "Informe o intervalo de dias, variável intervalo_dias");
        }

        return $dataMessage;

    }

    public function delete(Request $request){
        
        try{

            $item = Plano::find($request->id);
            if($item == null){
                return response()->json("Plano não encontrado!", 403);
            }
            $item->delete();
            return response()->json($item, 200);

        }catch(\Exception $e){
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }
}
