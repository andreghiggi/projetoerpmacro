<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotaServico;

class NfseWebHookController extends Controller
{
    public function index(Request $request){
        $chave = $request->chave;

        $item = NotaServico::where('chave', $chave)->first();

        if($request->codigo == 100){

            $item->estado = 'aprovado';
            $xml = base64_decode($request->xml);
            file_put_contents(public_path('xml_nota_servico/')."$item->chave.xml", $xml);
            $item->save();

        }
        return response()->json("ook", 200);
    }
}
