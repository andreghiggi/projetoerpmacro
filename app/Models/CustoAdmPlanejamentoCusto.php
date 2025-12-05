<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustoAdmPlanejamentoCusto extends Model
{
    use HasFactory;

    protected $fillable = [
        'planejamento_id', 'descricao', 'quantidade', 'valor_unitario', 'sub_total', 'observacao'
    ];
}
