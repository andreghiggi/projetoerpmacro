<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditoCliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'valor'
    ];
}
