<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaProduto extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'empresa_id', 'nome', 'cardapio', 'nome_en', 'nome_es', 'tipo_pizza', 'delivery', 'ecommerce',
        'hash_ecommerce', 'hash_delivery', 'reserva', 'categoria_id', '_id_import', 'status'
    ];

    public function produtos(){
        return $this->hasMany(Produto::class, 'categoria_id')->with(['pizzaValores', 'categoria'])->where('status', 1);
    }

    public function produtosCardapio(){
        return $this->hasMany(Produto::class, 'categoria_id')->with(['pizzaValores', 'categoria'])->where('status', 1)
        ->where('cardapio', 1);
    }

    public function produtosDelivery(){
        return $this->hasMany(Produto::class, 'categoria_id')->with(['pizzaValores', 'categoria'])->where('status', 1)
        ->where('delivery', 1);
    }

    public function subCategorias(){
        return $this->hasMany(CategoriaProduto::class, 'categoria_id');
    }

    public function categoria(){
        return $this->belongsTo(CategoriaProduto::class, 'categoria_id');
    }

}
