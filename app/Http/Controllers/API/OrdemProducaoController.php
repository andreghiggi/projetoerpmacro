<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrdemProducaoController extends Controller
{
    public function linha(Request $request){

        return view('ordem_producao.partials.linha');
    }
}
