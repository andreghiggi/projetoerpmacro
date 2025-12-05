<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoIfood extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'produto_id', 'ifood_id', 'ifood_id_aux', 'categoria_produto_ifood_id', 'descricao',
        'imagem', 'serving', 'nome', 'status', 'estoque', 'sellingOption_minimum', 'sellingOption_incremental', 
        'sellingOption_averageUnit', 'sellingOption_availableUnits', 'valor'
    ];
}
