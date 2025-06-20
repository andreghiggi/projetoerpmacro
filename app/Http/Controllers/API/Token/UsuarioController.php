<?php

namespace App\Http\Controllers\API\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsuarioEmpresa;

class UsuarioController extends Controller
{
    protected $prefix = 'usuarios';

    public function index(Request $request){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $data = User::where('usuario_empresas.empresa_id', $request->empresa_id)
        ->select('users.name as nome', 'users.email as email', 'users.id as id')
        ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'users.id')
        ->get();

        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);
        return response()->json($data, 200);
    }
}
