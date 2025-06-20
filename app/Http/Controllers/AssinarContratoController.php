<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContratoConfig;
use App\Models\Empresa;
use App\Models\ContratoEmpresa;
use App\Utils\ContratoUtil;

class AssinarContratoController extends Controller
{

    protected $contratoUtil;

    public function __construct(ContratoUtil $contratoUtil){
        $this->contratoUtil = $contratoUtil;
    }

    public function index(Request $request){
        $config = ContratoConfig::first();
        $empresa = Empresa::findOrFail($request->empresa_id);

        $texto = $this->contratoUtil->replaceTexto($config->texto, $empresa);

        return view('contrato.assinar', compact('texto'));
    }

    public function store(Request $request){
        $empresa = Empresa::findOrFail($request->empresa_id);
        $contrato = ContratoEmpresa::where('empresa_id', $request->empresa_id)
        ->first();

        $contrato->assinado = 1;
        $contrato->data_assinatura = date('Y-m-d H:i:s');
        $contrato->save();

        session()->flash("flash_success", "Contrato assinado!");
        return redirect()->route('home');

    }
}
