<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcaoLog;
use App\Models\Empresa;

class LogController extends Controller
{

    public function index(Request $request){

        $empresa = $request->empresa;
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $local = $request->get('local');
        $acao = $request->get('acao');

        $data = AcaoLog::
        when(!empty($empresa), function ($query) use ($empresa) {
            return $query->where('empresa_id', $empresa);
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($local), function ($query) use ($local) {
            return $query->where('local', $local);
        })
        ->when(!empty($acao), function ($query) use ($acao) {
            return $query->where('acao', $acao);
        })
        ->orderBy('created_at', 'desc')
        ->paginate('50');

        if($empresa){
            $empresa = Empresa::findOrFail($empresa);
        }
        return view('logs.index', compact('data', 'empresa'));
    }
}
