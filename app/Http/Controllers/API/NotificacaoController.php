<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificaoCardapio;
use App\Models\PedidoEcommerce;
use App\Models\PedidoDelivery;
use App\Models\Empresa;
use App\Models\Notificacao;

class NotificacaoController extends Controller
{
    public function index(Request $request){
        $data = NotificaoCardapio::
        where('empresa_id', $request->empresa_id)
        ->where('status', 0)
        ->get();
        if(sizeof($data) == 0){
            return response()->json("nada encontrado", 401);
        }

        return view('notificacao.index', compact('data'));
    }

    public function setStatus(Request $request){
        $item = NotificaoCardapio::with('pedido')->findOrfail($request->id);
        $item->status = !$item->status;
        $item->save();
        return response()->json($item, 200);
    }

    public function delivery(Request $request){
        $data = PedidoDelivery::
        where('empresa_id', $request->empresa_id)
        ->where('pedido_lido', 0)
        ->get();
        if(sizeof($data) == 0){
            return response()->json("", 200);
        }

        return view('notificacao.delivery', compact('data'));
    }

    public function ecommerce(Request $request){
        $data = PedidoEcommerce::
        where('empresa_id', $request->empresa_id)
        ->where('pedido_lido', 0)
        ->get();
        if(sizeof($data) == 0){
            return response()->json("", 200);
        }

        return view('notificacao.ecommerce', compact('data'));
    }

    public function alertas(Request $request){

        $empresa = Empresa::findOrFail($request->empresa_id);

        $notificacoesHoje = Notificacao::where('empresa_id', $request->empresa_id)
        ->whereDate('created_at', date('Y-m-d'))
        ->where('status', 1)
        ->where('visualizada', 0)
        ->get();

        $notificacoesOntem = Notificacao::where('empresa_id', $request->empresa_id)
        ->whereDate('created_at', date('Y-m-d', strtotime(date('Y-m-d'). '-1 days')))
        ->where('status', 1)
        ->where('visualizada', 0)
        ->get();

        $notificacoesAtrasadas = Notificacao::where('empresa_id', $request->empresa_id)
        ->whereDate('created_at', '<', date('Y-m-d', strtotime(date('Y-m-d'). '-1 days')))
        ->where('status', 1)
        ->where('visualizada', 0)
        ->orderBy('created_at', 'desc')
        ->get();
        // return response()->json(date('Y-m-d', strtotime(date('Y-m-d'). '-1 days')), 200);
        return view('notificacao.alertas', compact('notificacoesHoje', 'notificacoesOntem', 'notificacoesAtrasadas'));

    }

    public function alertaSuper(Request $request){

        $notificacoes = Notificacao::where('empresa_id', null)
        ->where('status', 1)
        ->where('visualizada', 0)
        ->get();

        return view('notificacao.alertas_super', compact('notificacoes'));

    }

}
