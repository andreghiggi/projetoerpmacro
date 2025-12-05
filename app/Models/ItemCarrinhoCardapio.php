<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCarrinhoCardapio extends Model
{
    use HasFactory;

    protected $fillable = [
        'carrinho_id', 'produto_id', 'quantidade', 'valor_unitario', 'sub_total', 'tamanho_id',
        'observacao'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function carrinho(){
        return $this->belongsTo(CarrinhoCardapio::class, 'carrinho_id');
    }

    public function adicionais(){
        return $this->hasMany(ItemCarrinhoAdicionalCardapio::class, 'item_carrinho_id');
    }

    public function sabores(){
        return $this->hasMany(ItemPizzaCarrinhoCardapio::class, 'item_carrinho_id');
    }

    public function tamanho(){
        return $this->belongsTo(TamanhoPizza::class, 'tamanho_id');
    }

    public function pizzas(){
        return $this->hasMany(ItemPizzaCarrinhoCardapio::class, 'item_carrinho_id');
    }
}
