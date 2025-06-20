<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ncm;
use App\Imports\ProdutoImport;
use Maatwebsite\Excel\Facades\Excel;

class NcmController extends Controller
{
    public function pesquisa(Request $request){
        $data = Ncm::orderBy('descricao', 'desc')
        ->where('descricao', 'like', "%$request->pesquisa%")
        ->get();
        return response()->json($data, 200);
    }

    public function valida(){
        if(!file_exists(public_path('tabela_ncm.xlsx'))){
            return response()->json("Arquivo tabela_ncm.xlsx não encontrado!", 404);
        }
        $data = Ncm::count();

        if($data == 0){
            return response()->json("validar", 403);
        }

        return response()->json("ok", 200);
    }

    public function carregar(){

        $file = file_get_contents(public_path('tabela_ncm.xlsx'));

        $rows = Excel::toArray(new ProdutoImport, public_path('tabela_ncm.xlsx'));

        foreach($rows[0] as $key => $line){

            if(isset($line[0])){
                $codigo = trim($line[0]);
                $descricao = trim($line[1]);

                if($key > 4){
                    $descricao = str_replace("--", "", $descricao);
                    $descricao = str_replace("-", "", $descricao);

                    Ncm::create([
                        'codigo' => $codigo,
                        'descricao' => "$codigo - $descricao"
                    ]);
                }
            }
        }
        return response()->json("ok", 200);

    }
}
