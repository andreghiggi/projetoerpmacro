<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TamanhoPizza extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nome', 'maximo_sabores', 'quantidade_pedacos', 'status', 'nome_en', 'nome_es'
    ];

    public function getInfoAttribute()
    {
        return "$this->nome ($this->quantidade_pedacos) fatias - até $this->maximo_sabores sabor(es)";
    }

    public function produtos(){
        return $this->hasMany(ProdutoPizzaValor::class, 'tamanho_id');
    }

    public function getValorDaPizza($produto_id){
        $pizza = ProdutoPizzaValor::
        where('produto_id', $produto_id)
        ->where('tamanho_id', $this->id)
        ->first();
        if($pizza != null){
            return __moeda($pizza->valor);
        }
        return '';
    }

}
