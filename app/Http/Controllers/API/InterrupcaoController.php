<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotivoInterrupcao;
class InterrupcaoController extends Controller
{
    public function storeMotivo(Request $request){
        $item = MotivoInterrupcao::create([
            'motivo' => $request->motivo,
            'empresa_id' => $request->empresa_id
        ]);
        return response()->json($item, 200);
    }
}
