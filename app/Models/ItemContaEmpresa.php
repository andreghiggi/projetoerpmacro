<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemContaEmpresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'conta_id', 'descricao', 'tipo_pagamento', 'valor', 'caixa_id', 'tipo', 'saldo_atual'
    ];

    public function conta(){
        return $this->belongsTo(ContaEmpresa::class, 'conta_id');
    }

    public function caixa(){
        return $this->belongsTo(Caixa::class, 'caixa_id');
    }
}
