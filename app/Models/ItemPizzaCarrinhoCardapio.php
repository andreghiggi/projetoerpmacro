<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPizzaCarrinhoCardapio extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_carrinho_id', 'produto_id'
    ];

    public function sabor(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
    
}
