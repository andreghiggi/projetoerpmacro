<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WoocommercePedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'pedido_id', 'rua', 'numero', 'bairro', 'cidade', 'uf', 'cep', 'total', 'valor_frete',
        'desconto', 'observacao', 'nome', 'email', 'documento', 'nfe_id', 'tipo_pagamento', 
        'status', 'numero_pedido', 'data', 'venda_id', 'cliente_id'
    ];

    public function itens(){
        return $this->hasMany(WoocommerceItemPedido::class, 'pedido_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function nfe(){
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }
}
