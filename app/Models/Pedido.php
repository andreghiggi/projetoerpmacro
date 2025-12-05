<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'cliente_id', 'comanda', 'observacao', 'tipo_pagamento', 'data_fechamento', 'total',
        'status', 'cliente_nome', 'cliente_fone', 'mesa', 'funcionario_id', 'percentual_taxa_servico', 'mesa_id',
        'local_pedido', 'session_cart_cardapio', 'confirma_mesa', 'session_cart_user', 'desconto', 'acrescimo'
    ];

    public function itens(){
        return $this->hasMany(ItemPedido::class, 'pedido_id')->with(['produto', 'adicionais', 'tamanho', 'pizzas', 'funcionario']);
    }

    public function _mesa(){
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function itensServico(){
        return $this->hasMany(ItemPedidoServico::class, 'pedido_id')->with(['servico']);
    }

    public function notificacoes(){
        return $this->hasMany(NotificaoCardapio::class, 'pedido_id');
    }

    public function countItens(){
        return sizeof($this->itens);
    }

    public function totalClientes(){
        return ItemPedido::where('pedido_id', $this->id)
        ->select('nome_cardapio')->distinct('nome_cardapio')->count();
    }

    public function somaProdutos(){
        $total = 0;
        foreach($this->itens as $i){
            $total += $i->sub_total;
        }
        return $total;
    }

    public function sumTotal(){
        $total = 0;
        foreach($this->itens as $i){
            $total += $i->sub_total;
        }

        foreach($this->itensServico as $i){
            $total += $i->sub_total;
        }

        $this->total = $total;
        $this->save();
    }

}
