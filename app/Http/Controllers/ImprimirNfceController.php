<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nfce;
use NFePHP\DA\NFe\Danfce;

class ImprimirNfceController extends Controller
{
    public function imprimir($chave){
        $item = Nfce::where('chave', $chave)->first();
        if (file_exists(public_path('xml_nfce/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfce/') . $item->chave . '.xml');
            $danfe = new Danfce($xml, $item);
            $pdf = $danfe->render();
            return response($pdf)
            ->header('Content-Type', 'application/pdf');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }
}
