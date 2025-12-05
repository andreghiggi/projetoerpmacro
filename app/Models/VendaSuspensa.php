<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaSuspensa extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'cliente_id', 'total', 'desconto', 'acrescimo', 'observacao', 'tipo_pagamento', 'local_id', 'user_id',
        'funcionario_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function itens()
    {
        return $this->hasMany(ItemVendaSuspensa::class, 'venda_id')->with('produto');
    }
}
