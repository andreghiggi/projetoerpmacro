<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContadorEmpresa;
use App\Models\Empresa;

class ContadorController extends Controller
{
    public function empresas(Request $request){
        $contador_id = $request->contador_id;

        $data = ContadorEmpresa::where('contador_id', $contador_id)->get();
        $contador = Empresa::findOrFail($contador_id);

        return view('contador.partials.tabela_contador', compact('data', 'contador'));
    }
}
