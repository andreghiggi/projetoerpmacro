<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedidaCte extends Model
{
    use HasFactory;

    protected $fillable = [
        'cte_id', 'tipo_medida', 'quantidade', 'cod_unidade'
    ];
}
