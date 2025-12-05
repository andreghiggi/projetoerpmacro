<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{

    public function start($id)
    {
        $user = User::findOrFail($id);
        session(['impersonate' => $id]);
        Auth::loginUsingId($id);

        // session()->flush();
        // session()->regenerate(true);

        session()->flash("flash_success", "Logado como $user->name!");
        return redirect()->route('home');
    }

    public function stop()
    {
        if (session()->has('impersonate')) {
            $originalId = session('original_user');
            session()->forget(['impersonate', 'original_user']);
            Auth::loginUsingId($originalId);

            session()->flush();
            session()->regenerate(true);

            $user = Auth::user();

            session()->flash("flash_success", "Retornado ao modo SuperAdmin.");
        }
        return redirect()->route('usuario-super.index');
    }

}
