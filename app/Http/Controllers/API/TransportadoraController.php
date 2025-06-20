<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transportadora;
use Illuminate\Http\Request;

class TransportadoraController extends Controller
{
    public function find($id){
        $item = Transportadora::with('cidade')->findOrFail($id);
        return response()->json($item, 200);
    }
}
