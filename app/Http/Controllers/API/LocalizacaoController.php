<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Localizacao;

class LocalizacaoController extends Controller
{
    public function findNumberDoc(Request $request){
        $item = Localizacao::findOrFail($request->local_id);
        $firstLocation = Localizacao::where('empresa_id', $item->empresa_id)->first();
        if($item == $firstLocation){
            $item = $item->empresa;
        }
        $data = [
            'numero_nfe' => $item->ambiente == 2 ? $item->numero_ultima_nfe_homologacao+1 : $item->numero_ultima_nfe_producao+1,
            'numero_nfce' => $item->ambiente == 2 ? $item->numero_ultima_nfce_homologacao+1 : $item->numero_ultima_nfce_producao+1,
            'numero_cte' => $item->ambiente == 2 ? $item->numero_ultima_cte_homologacao+1 : $item->numero_ultima_cte_producao+1,
            'numero_mdfe' => $item->ambiente == 2 ? $item->numero_ultima_mdfe_homologacao+1 : $item->numero_ultima_mdfe_producao+1,
        ];
        return response()->json($data, 200);
    }
}
