<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistroController extends Controller
{
    public function index(){
        $codigo = 'BR512025000339-0';
        $protocolo = '870250007519';

        return view('registro', compact('codigo', 'protocolo'));
    }
}
