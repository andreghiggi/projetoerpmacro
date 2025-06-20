<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contigencia;
use App\Models\Nfce;

class NfceContigenciaController extends Controller
{
    public function index(Request $request){
        $contigencia = $this->getContigencia($request->empresa_id);
        if($contigencia != null){
            session()->flash('flash_error', 'Desative a contigência do sistema para acessar essa tela!');
            return redirect()->back();
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = Nfce::where('contigencia', 1)
        ->where('reenvio_contigencia', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->where('empresa_id', $request->empresa_id)->get();

        return view('nfce.contigencia', compact('data'));
    }

    private function getContigencia($empresa_id){
        $active = Contigencia::
        where('empresa_id', $empresa_id)
        ->where('status', 1)
        ->where('documento', 'NFCe')
        ->first();
        return $active;
    }
}
