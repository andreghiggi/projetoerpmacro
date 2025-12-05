<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdemServico;

class OrdemServicoViewController extends Controller
{
    public function index($hash){
        $ordem = OrdemServico::where('hash_link', $hash)->first();

        $empresa = $ordem->empresa;

        return view('ordem_servico.link', compact('ordem', 'empresa'));
    }
}
