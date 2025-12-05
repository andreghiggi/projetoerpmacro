<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestaoCustoProducaoProduto extends Model
{
    use HasFactory;

    protected $fillable = [
        'gestao_custo_id', 'produto_id', 'quantidade', 'valor_unitario', 'sub_total', 'observacao'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
