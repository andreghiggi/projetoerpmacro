<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TefMultiPlusCard;
use App\Models\User;

class TefConfigController extends Controller
{
    public function index(Request $request){
        $data = TefMultiPlusCard::where('empresa_id', $request->empresa_id)
        ->get();

        return view('tef_config.index', compact('data'));
    }

    public function create(Request $request){
        $usuarios = User::where('usuario_empresas.empresa_id', $request->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->get();

        return view('tef_config.create', compact('usuarios'));

    }

    public function edit(Request $request, $id){

        $item = TefMultiPlusCard::findOrFail($id);
        $usuarios = User::where('usuario_empresas.empresa_id', $request->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->get();

        return view('tef_config.edit', compact('usuarios', 'item'));

    }

    public function store(Request $request){

        try{
            TefMultiPlusCard::create($request->all());
            session()->flash("flash_success", "Configuração salva!");
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('tef-config.index');
    }

    public function update(Request $request, $id){

        $item = TefMultiPlusCard::where('empresa_id', $request->empresa_id)
        ->first();
        try{

            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada!");
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('tef-config.index');
    }
}
