<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoVendiZap extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'numero_pedido', 'nome', 'documento', 'telefone', 'email', 'cep', 'rua', 'numero', 'bairro',
        'cidade', 'uf', 'complemento', 'total', 'observacao', 'entrega', 'taxa_entrega', 'taxa_retirada', '_id', 'hash',
        'codigo_link_rastreio', 'tipo_pagamento', 'data', 'cliente_id'
    ];

    public function itens(){
        return $this->hasMany(ItemPedidoVendiZap::class, 'pedido_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

}
