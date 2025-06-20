<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVendaSuspensa extends Model
{
    use HasFactory;

    protected $fillable = [
        'venda_id', 'produto_id', 'variacao_id', 'quantidade', 'valor_unitario', 'sub_total'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function produtoVariacao(){
        return $this->belongsTo(ProdutoVariacao::class, 'variacao_id');
    }

}
