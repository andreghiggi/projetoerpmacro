<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FilaEnvioCron;

class MensagemCrmLogController extends Controller
{
    public function index(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $status = $request->get('status');
        $tipo = $request->get('tipo');

        $data = FilaEnvioCron::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when(!empty($status), function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->when(!empty($tipo), function ($query) use ($tipo) {
            return $query->where('tipo', $tipo);
        })
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

        return view('mensagem_padrao_crm.logs', compact('data'));
    }

    public function destroy($id)
    {
        $item = FilaEnvioCron::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->delete();
            session()->flash('flash_success', 'Registro removido com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível deletar: ' . $e->getMessage());
        }
        return redirect()->back();
    }
}
