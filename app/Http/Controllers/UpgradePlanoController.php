<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\Empresa;
use App\Models\Pagamento;
use App\Models\ConfiguracaoSuper;

class UpgradePlanoController extends Controller
{
    public function index(Request $request){
        $empresa = Empresa::findOrFail($request->empresa_id);

        $segmento_id = sizeof($empresa->segmentos) > 0 ?$empresa->segmentos[0]->segmento_id : null;

        $planos = Plano::where('status', 1)
        ->where('visivel_clientes', 1)
        ->when($segmento_id, function ($q) use ($segmento_id) {
            return $q->where('segmento_id', $segmento_id);
        })
        ->where('valor', '>', 0)
        ->get();

        $config = ConfiguracaoSuper::first();

        if($config == null){
            session()->flash("flash_error", "Opção de pagamento não configurada!");
            return redirect()->back();
        }

        if((!$config->mercadopago_public_key && !$config->mercadopago_access_token) && !$config->asaas_token){
            session()->flash("flash_error", "Opção de pagamento não configurada!");
            return redirect()->back();
        }

        return view('payment.index', compact('planos', 'config'));

    }
}
