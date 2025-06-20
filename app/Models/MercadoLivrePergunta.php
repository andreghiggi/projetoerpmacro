<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MercadoLivrePergunta extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', '_id', 'item_id', 'status', 'texto', 'data', 'resposta'
    ];

    public function anuncio(){
        return $this->belongsTo(Produto::class, 'item_id', 'mercado_livre_id');
    }

}
