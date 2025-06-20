<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'cliente_id', 'comanda', 'observacao', 'tipo_pagamento', 'data_fechamento', 'total',
        'status', 'cliente_nome', 'cliente_fone', 'mesa', 'funcionario_id'
    ];

    public function itens(){
        return $this->hasMany(ItemPedido::class, 'pedido_id')->with(['produto', 'adicionais', 'tamanho', 'pizzas', 'funcionario']);
    }

    public function itensServico(){
        return $this->hasMany(ItemPedidoServico::class, 'pedido_id')->with(['servico']);
    }

    public function countItens(){
        return sizeof($this->itens);
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
