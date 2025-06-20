<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cidade;
use App\Models\PlanoConta;
use App\Models\ContaEmpresa;
use App\Models\ContaBoleto;
use App\Models\VideoSuporte;
use App\Models\ModeloEtiqueta;

class HelperController extends Controller
{
    public function cidadePorNome($nome){
        $cidade = Cidade::
        where('nome', $nome)
        ->first();

        return response()->json($cidade, 200);
    }

    public function videoSuporte(Request $request){
        $item = VideoSuporte::where('url_servidor', $request->url)
        ->first();
        if($item == null){
            return response()->json("", 200);
        }
        return view('video_suporte.button', compact('item'));
    }

    public function cidadePorCodigoIbge($codigo){
        $cidade = Cidade::
        where('codigo', $codigo)
        ->first();

        return response()->json($cidade, 200);
    }

    public function cidadePorId($id){
        $cidade = Cidade::findOrFail($id);

        return response()->json($cidade, 200);
    }

    public function buscaCidades(Request $request){
        $data = Cidade::
        where('nome', 'like', "%$request->pesquisa%")
        ->get();

        return response()->json($data, 200);
        
    }

    public function planoContas(Request $request){
        $data = PlanoConta::
        where('descricao', 'like', "%$request->pesquisa%")
        ->where('empresa_id', $request->empresa_id)
        ->get();

        return response()->json($data, 200); 
    }

    public function contasEmpresa(Request $request){
        $data = ContaEmpresa::
        where('nome', 'like', "%$request->pesquisa%")
        ->where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->get();

        return response()->json($data, 200); 
    }

    public function contasEmpresaCount(Request $request){
        $data = ContaEmpresa::
        where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->count();

        return response()->json($data, 200); 
    }

    public function contaBoleto(Request $request){
        $data = ContaBoleto::findOrFail($request->conta_boleto_id);

        return response()->json($data, 200); 
    }

    public function etiqueta(Request $request){
        $item = ModeloEtiqueta::findOrFail($request->modelo_id);
        return response()->json($item, 200);
    }

}
