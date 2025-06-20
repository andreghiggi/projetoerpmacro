<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificaoCardapio extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'pedido_id', 'mesa', 'comanda', 'tipo', 'tipo_pagamento', 'avaliacao', 'observacao', 'status'
    ];

    public function pedido(){
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

}
