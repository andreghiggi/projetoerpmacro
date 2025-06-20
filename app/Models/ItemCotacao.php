<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCotacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'cotacao_id', 'produto_id', 'valor_unitario', 'quantidade', 'sub_total', 'observacao'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
