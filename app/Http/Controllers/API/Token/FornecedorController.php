<?php

namespace App\Http\Controllers\API\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fornecedor;
use App\Models\Cidade;

class FornecedorController extends Controller
{
    protected $prefix = 'fornecedores';

    public function index(Request $request){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $data = Fornecedor::where('empresa_id', $request->empresa_id)
        ->select('id', 'razao_social', 'nome_fantasia', 'cpf_cnpj', 'ie', 'contribuinte', 'consumidor_final',
            'email', 'telefone', 'cidade_id', 'rua', 'cep', 'numero', 'bairro', 'complemento')
        ->with('cidade')
        ->get();
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);
        return response()->json($data, 200);
    }

    public function find(Request $request, $id){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $empresa_id = $request->empresa_id;
        $item = Fornecedor::where('empresa_id', $empresa_id)
        ->select('id', 'razao_social', 'nome_fantasia', 'cpf_cnpj', 'ie', 'contribuinte', 'consumidor_final',
            'email', 'telefone', 'cidade_id', 'rua', 'cep', 'numero', 'bairro', 'complemento')
        ->with('cidade')
        ->findOrFail($id);
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);

        return response()->json($item, 200);
    }

    public function store(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".create")){
            return response()->json("Permissão negada!", 403);
        }
        try{
            $empresa_id = $request->empresa_id;

            $validaRequest = $this->validaRequest($request);
            if(sizeof($validaRequest) > 0){
                return response()->json($validaRequest, 403);
            }
            
            $cidade = Cidade::where('nome', $request->cidade)
            ->where('uf', $request->uf)->first();

            if($cidade == null){
                return response()->json("Cidade não encontrada!", 403);
            }

            $request->merge([
                'cidade_id' => $cidade->id
            ]);

            $item = Fornecedor::create($request->all());
            $item = Fornecedor::where('empresa_id', $empresa_id)
            ->select('id', 'razao_social', 'nome_fantasia', 'cpf_cnpj', 'ie', 'contribuinte', 'consumidor_final',
                'email', 'telefone', 'cidade_id', 'rua', 'cep', 'numero', 'bairro', 'complemento')
            ->with('cidade')
            ->findOrFail($item->id);
            __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'create', $this->prefix);
            return response()->json($item, 200);

        }catch(\Exception $e){
            __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'create', $this->prefix);
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    private function validaRequest($request){
        $dataMessage = [];
        if(!isset($request->razao_social) || $request->razao_social == ""){
            array_push($dataMessage, "Informe a razão social");
        }
        if(!isset($request->rua) || $request->rua == ""){
            array_push($dataMessage, "Informe a rua");
        }
        if(!isset($request->numero) || $request->numero == ""){
            array_push($dataMessage, "Informe a número");
        }
        if(!isset($request->bairro) || $request->bairro == ""){
            array_push($dataMessage, "Informe a bairro");
        }
        if(!isset($request->cep) || $request->cep == ""){
            array_push($dataMessage, "Informe a CEP");
        }
        if(!isset($request->cidade) || $request->cidade == ""){
            array_push($dataMessage, "Informe a cidade");
        }
        if(!isset($request->uf) || $request->uf == ""){
            array_push($dataMessage, "Informe a uf");
        }
        return $dataMessage;
    }

    public function update(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".update")){
            return response()->json("Permissão negada!", 403);
        }
        try{
            $empresa_id = $request->empresa_id;

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

            $item = Fornecedor::where('empresa_id', $empresa_id)
            ->findOrFail($request->id);

            $item->fill($request->all())->save();

            __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'update', $this->prefix);

            $item = Fornecedor::where('empresa_id', $empresa_id)
            ->select('id', 'razao_social', 'nome_fantasia', 'cpf_cnpj', 'ie', 'contribuinte', 'consumidor_final',
                'email', 'telefone', 'cidade_id', 'rua', 'cep', 'numero', 'bairro', 'complemento')
            ->with('cidade')
            ->findOrFail($item->id);
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

            $item = Fornecedor::where('empresa_id', $request->empresa_id)
            ->select('id', 'razao_social', 'nome_fantasia', 'cpf_cnpj', 'ie', 'contribuinte', 'consumidor_final',
                'email', 'telefone', 'cidade_id', 'rua', 'cep', 'numero', 'bairro', 'complemento')
            ->with('cidade')
            ->find($request->id);
            if($item == null){
                return response()->json("Fornecedor não encontrado!", 403);
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
