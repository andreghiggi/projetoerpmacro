<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContratoConfig;
use App\Models\ContratoEmpresa;
use App\Models\Empresa;

class ContratoConfigController extends Controller
{
    public function index(){
        $item = ContratoConfig::first();
        return view('config_contrato.index', compact('item'));
    }

    public function store(Request $request){
        $item = ContratoConfig::first();

        try{
            if ($item != null) {
                $item->fill($request->all())->save();
            }else{
                ContratoConfig::create($request->all());
            }
            session()->flash("flash_success", "Configuração salva!");
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function list(Request $request){

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $estado = $request->get('estado');
        $empresa = $request->get('empresa');

        $data = ContratoEmpresa::
        orderBy('id', 'desc')
        ->when(!empty($empresa), function ($query) use ($empresa) {
            return $query->where('empresa_id', $empresa);
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })

        ->when($estado, function ($query) use ($estado) {
            if($estado == 'pendente'){
                return $query->where('assinado', 0);
            }else if($estado == 'assinado'){
                return $query->where('assinado', 1);
            }
        })
        ->paginate(__itensPagina());

        if($empresa){
            $empresa = Empresa::findOrFail($empresa);
        }

        return view('config_contrato.list', compact('data', 'empresa'));
    }
}
