<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConfigGeral;

class ConfigGeralController extends Controller
{
    public function index(Request $request){
        $item = ConfigGeral::where('empresa_id', $request->empresa_id)->first();
        return response()->json($item, 200);
    }
}
