<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;

class ImpersonateContadorController extends Controller
{
    public function start($id)
    {
        $empresa = Empresa::findOrFail($id);

        if(!$empresa->usuarios[0]){
            session()->flash("flash_warning", "Problema ao buscar usuário da empresa.");
            return redirect()->back();
        }
        $user = $empresa->usuarios[0]->usuario;

        session(['impersonate_contador' => $user->id]);
        Auth::loginUsingId($id);

        // session()->flush();
        // session()->regenerate(true);

        session()->flash("flash_success", "Logado como $user->name!");
        return redirect()->route('home');
    }

    public function stop()
    {

        if (session()->has('impersonate_contador')) {
            $originalId = session('original_user');
            session()->forget(['impersonate_contador', 'original_user']);
            Auth::loginUsingId($originalId);

            session()->flush();
            session()->regenerate(true);

            $user = Auth::user();

            session()->flash("flash_success", "Retornado ao modo Contador.");
        }
        return redirect()->route('contador-empresas.index');
    }
}
