<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\FaturaNfe;
use App\Models\FaturaNfce;
use App\Models\Empresa;

class TesteController extends Controller
{
    public function index(){

        return view('teste');

    }
}
