<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TefController extends Controller
{

    public function index(Request $request){
        return view('dimensao_teste');
    }

}
