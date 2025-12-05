<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaturaCliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'tipo_pagamento', 'dias_vencimento'
    ];
}
