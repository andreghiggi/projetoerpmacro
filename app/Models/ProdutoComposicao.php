<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoComposicao extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'ingrediente_id', 'quantidade'
    ];

    public function ingrediente(){
        return $this->belongsTo(Produto::class, 'ingrediente_id');
    }
}
