<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConectaVendaConfig extends Model
{
    use HasFactory;

    protected $table = 'conecta_venda_config';
    protected $fillable = [
        'client_id', 'client_secret', 'empresa_id'
    ];
}
