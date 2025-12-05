<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestaoCustoProducaoOutroCusto extends Model
{
    use HasFactory;

    protected $fillable = [
        'gestao_custo_id', 'descricao', 'quantidade', 'valor_unitario', 'sub_total', 'observacao'
    ];
}
