<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CupomDescontoCliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'empresa_id', 'cupom_id', 'pedido_id'
    ];
}
