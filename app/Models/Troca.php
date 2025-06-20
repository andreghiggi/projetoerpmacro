<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Troca extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nfce_id', 'observacao', 'total', 'numero_sequencial', 'codigo', 'valor_troca', 'valor_original',
        'tipo_pagamento'
    ];

    public function nfce()
    {
        return $this->belongsTo(Nfce::class, 'nfce_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function itens()
    {
        return $this->hasMany(ItemTroca::class, 'troca_id')->with('produto');
    }
}
