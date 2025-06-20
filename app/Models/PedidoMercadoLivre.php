<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoMercadoLivre extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', '_id', 'tipo_pagamento', 'status', 'total', 'valor_entrega', 'nickname',
        'seller_id', 'entrega_id', 'data_pedido', 'comentario', 'nfe_id', 'rua_entrega',
        'numero_entrega', 'cep_entrega', 'bairro_entrega', 'cidade_entrega', 'comentario_entrega',
        'cliente_id'
    ];

    public function itens(){
        return $this->hasMany(ItemPedidoMercadoLivre::class, 'pedido_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function nfe(){
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }
}
