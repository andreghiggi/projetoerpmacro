<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoPizzaValor extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'tamanho_id', 'valor'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function tamanho(){
        return $this->belongsTo(TamanhoPizza::class, 'tamanho_id');
    }

}
