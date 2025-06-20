<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contigencia;
use App\Models\Empresa;
use NFePHP\NFe\Factories\Contingency;

class ContigenciaController extends Controller
{
    public function index(Request $request){
        $data = Contigencia::
        where('empresa_id', $request->empresa_id)
        ->orderBy('id', 'desc')
        ->get();

        return view('contigencia.index', compact('data'));
    }

    public function create(){
        return view('contigencia.create');
    }

    public function store(Request $request){

        $active = Contigencia::
        where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('documento', $request->documento)
        ->first();
            // dd($request->all());

        if($active){
            session()->flash('flash_error', "Já existe uma contigência para $request->documento ativada!");
            return redirect()->back();
        }
        try{

            $config = Empresa::findOrFail($request->empresa_id);

            $contingency = new Contingency();

            $acronym = $config->cidade->uf;
            $motive = $request->motivo;
            $type = $request->tipo;

            $status_retorno = $contingency->activate($acronym, $motive, $type);

            $item = Contigencia::create([
                'empresa_id' => $request->empresa_id,
                'status' => 1,
                'tipo' => $request->tipo,
                'documento' => $request->documento,
                'motivo' => $request->motivo,
                'status_retorno' => ''
            ]);
            $item->status_retorno = $status_retorno;
            $item->save();
            session()->flash("flash_success", "Contigencia ativada!");
        }catch(\Exception $e){
            // echo $e->getMessage();
            // die;
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('contigencia.index');
    }

    public function desactive($id){
        $item = Contigencia::findOrFail($id);
        $item->status = 0;

        $contingency = new Contingency($item->status_retorno);
        $status = $contingency->deactivate();

        $item->save();
        session()->flash("flash_success", "Contigencia ddesativada!");
        return redirect()->back();

    }

}
