<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemListaPreco extends Model
{
    use HasFactory;

    protected $fillable = [
        'lista_id', 'produto_id', 'valor', 'percentual_lucro'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
