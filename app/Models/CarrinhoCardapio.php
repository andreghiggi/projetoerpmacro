<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrinhoCardapio extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'estado', 'valor_total', 'session_cart_cardapio', 'observacao', 'cliente_nome', 'session_cart_user'
    ];

    public function itens(){
        return $this->hasMany(ItemCarrinhoCardapio::class, 'carrinho_id');
    }

}
