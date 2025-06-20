<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuporteController extends Controller
{
    public function index(Request $request){
        return view('suporte.index');
    }
}
