<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function setSidebar(Request $request){
        $user = User::findOrFail($request->usuario_id);
        $user->sidebar_active = !$user->sidebar_active;
        $user->save();
        return response()->json("", 200);
    }

    public function usuarios(Request $request){
        try {
            $data = User::where('usuario_empresas.empresa_id', $request->empresa_id)
            ->select('users.*')
            ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'users.id')
            ->where('users.name', 'LIKE', "%$request->pesquisa%")
            ->get();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function updateTheme(Request $request){
        $user = User::findOrFail($request->usuario_id);
        $user->tema_padrao = $request->tema;
        $user->save();
        return response()->json($user->tema_padrao, 200);
    }

}
