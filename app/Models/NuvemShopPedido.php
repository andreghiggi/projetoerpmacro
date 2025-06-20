<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NuvemShopPedido extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'pedido_id', 'rua', 'numero', 'bairro', 'cidade', 'cep', 'total', 'cliente_id',
        'observacao', 'nome', 'email', 'documento', 'empresa_id', 'subtotal', 'desconto',
        'nfe_id', 'status_envio', 'gateway', 'status_pagamento', 'data', 'venda_id', 'valor_frete'
    ];

    public function getDate(){
        $data = substr($this->data, 0, 16);
        return \Carbon\Carbon::parse($data)->format('d/m/Y H:i');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id', 'nuvem_shop_id');
    }

    public function itens(){
        return $this->hasMany(NuvemShopItemPedido::class, 'pedido_id');
    }

    public function venda(){
        return $this->belongsTo(Venda::class, 'venda_id');
    }

    public function nfe(){
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }
}
