<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCarrinhoDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'carrinho_id', 'produto_id', 'quantidade', 'valor_unitario', 'sub_total', 'tamanho_id',
        'observacao', 'servico_id'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function servico(){
        return $this->belongsTo(Servico::class, 'servico_id');
    }

    public function carrinho(){
        return $this->belongsTo(CarrinhoDelivery::class, 'carrinho_id');
    }

    public function adicionais(){
        return $this->hasMany(ItemCarrinhoAdicionalDelivery::class, 'item_carrinho_id');
    }

    public function sabores(){
        return $this->hasMany(ItemPizzaCarrinho::class, 'item_carrinho_id');
    }

    public function tamanho(){
        return $this->belongsTo(TamanhoPizza::class, 'tamanho_id');
    }

    public function pizzas(){
        return $this->hasMany(ItemPizzaCarrinho::class, 'item_carrinho_id');
    }
}
