<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificaoCardapio;

class AvaliacaoCardapioController extends Controller
{
    public function index(Request $request){

        $nome = $request->nome;
        $data = NotificaoCardapio::
        where('tipo', 'fechar_mesa')
        ->select('notificao_cardapios.*')
        ->when($nome != '', function ($query) use ($nome) {
            return $query->join('pedidos', 'pedidos.id', '=', 'notificao_cardapios.pedido_id')
            ->where('pedidos.cliente_nome', 'like', "%$nome%");
        })
        ->whereNotNull('avaliacao')
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

        return view('cardapio.avaliacoes.index', compact('data'));
    }
}
