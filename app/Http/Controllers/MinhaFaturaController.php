<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceiroBoleto;

class MinhaFaturaController extends Controller
{

    public function index(Request $request){
        $data = FinanceiroBoleto::where('empresa_id', $request->empresa_id)
        ->get();

        return view('minhas_faturas.index', compact('data'));
    }
}
