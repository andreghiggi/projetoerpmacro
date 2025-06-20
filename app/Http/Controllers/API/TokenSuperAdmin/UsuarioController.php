<?php

namespace App\Http\Controllers\API\TokenSuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsuarioEmpresa;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request){

        $empresa_id = $request->empresa_id;
        if($empresa_id){
            $data = UsuarioEmpresa::where('empresa_id', $empresa_id)
            ->select('users.*')
            ->join('users', 'users.id', '=', 'usuario_empresas.usuario_id')
            ->get()
            ->each(function($row)
            {
                $row->setHidden(['notificacao_cardapio', 'tipo_contador', 'notificacao_marketplace', 'notificacao_ecommerce', 'password', 'imagem', 'remember_token', 'email_verified_at', 'sidebar_active']);
            });

        }else{
            $data = User::all()
            ->each(function($row)
            {
                $row->setHidden(['notificacao_cardapio', 'tipo_contador', 'notificacao_marketplace', 'notificacao_ecommerce', 'password', 'imagem', 'remember_token', 'email_verified_at', 'sidebar_active']);
            });
        }
        return response()->json($data, 200);
    }

    public function find(Request $request, $id){

        $item = User::find($id);
        if($item == null){
            return response()->json("Usuário não encontrado!", 403);
        }
        $item->setHidden(['notificacao_cardapio', 'tipo_contador', 'notificacao_marketplace', 'notificacao_ecommerce', 'password', 'imagem', 'remember_token', 'email_verified_at', 'sidebar_active']);
        return response()->json($item, 200);
    }

    public function store(Request $request){

        try{
            $validaRequest = $this->validaRequest($request);
            if(sizeof($validaRequest) > 0){
                return response()->json($validaRequest, 403);
            }

            $email = $request->email;
            $user = User::where('email', $email)->first();
            if($user != null){
                return response()->json("Email já utilizado!", 403);
            }

            $empresa = Empresa::find($request->empresa_id);
            if($empresa == null){
                return response()->json("Empresa não encontrado!", 403);
            }

            $usuario = User::create([
                'name' => $request->nome,
                'email' => $email,
                'password' => Hash::make($request->senha),
            ]);

            UsuarioEmpresa::create([
                'empresa_id' => $empresa->id,
                'usuario_id' => $usuario->id ?? null
            ]);

            $usuario->setHidden(['notificacao_cardapio', 'tipo_contador', 'notificacao_marketplace', 'notificacao_ecommerce', 'password', 'imagem', 'remember_token', 'email_verified_at', 'sidebar_active']);
            return response()->json($usuario, 200);

        }catch(\Exception $e){
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    public function update(Request $request){
        
        try{

            $item = User::find($request->id);
            if($item == null){
                return response()->json("Usuário não encontrado!", 403);
            }
            $item->password = isset($request->senha) ? Hash::make($request->senha) : $item->password;
            $item->name = isset($request->nome) ? $request->nome : $item->nome;
            $item->save();
            $item->setHidden(['notificacao_cardapio', 'tipo_contador', 'notificacao_marketplace', 'notificacao_ecommerce', 'password', 'imagem', 'remember_token', 'email_verified_at', 'sidebar_active']);
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
        if(!isset($request->email) || $request->email == ""){
            array_push($dataMessage, "Informe o email");
        }
        if(!isset($request->senha) || $request->senha == ""){
            array_push($dataMessage, "Informe a senha");
        }
        if(!isset($request->empresa_id) || $request->empresa_id == ""){
            array_push($dataMessage, "Informe a empresa ID, variável empresa_id");
        }
        return $dataMessage;
    }

    public function delete(Request $request){
        
        try{

            $item = User::find($request->id);
            if($item == null){
                return response()->json("Usuário não encontrado!", 403);
            }
            $item->setHidden(['notificacao_cardapio', 'tipo_contador', 'notificacao_marketplace', 'notificacao_ecommerce', 'password', 'imagem', 'remember_token', 'email_verified_at', 'sidebar_active']);
            $item->acessos()->delete();
            $item->locais()->delete();
            if($item->empresa){
                $item->empresa->delete();
            }
            $item->delete();
            return response()->json($item, 200);

        }catch(\Exception $e){
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }
}
