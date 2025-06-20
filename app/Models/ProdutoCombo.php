<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoCombo extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'quantidade', 'valor_compra', 'sub_total', 'item_id'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function produtoDoCombo(){
        return $this->belongsTo(Produto::class, 'item_id');
    }
}
