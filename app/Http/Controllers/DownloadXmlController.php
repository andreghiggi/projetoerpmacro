<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadXmlController extends Controller
{
    public function download($chave){
        if (file_exists(public_path('xml_nfce/') . $chave . '.xml')) {
            return response()->download(public_path('xml_nfce/') . $chave . '.xml');
        }elseif (file_exists(public_path('xml_nfe/') . $chave . '.xml')) {
            return response()->download(public_path('xml_nfe/') . $chave . '.xml');
        }else{
            echo "Arquivo não encontrado!";
        }
    }
}
