<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\ConfiguracaoSuper;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    public function index(){        

        if(Auth::user() != null){
            return redirect()->route('home');
        }

        $config = ConfiguracaoSuper::first();
        if($config == null || $config->landing_page == 0){
            return redirect()->route('login');
        }

        $planos = Plano::where('status', 1)
        ->where('visivel_clientes', 1)
        ->get();

        return view('landing_page', compact('planos'));
    }
}
