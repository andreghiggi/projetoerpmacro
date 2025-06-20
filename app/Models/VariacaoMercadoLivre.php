<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariacaoMercadoLivre extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', '_id', 'quantidade', 'valor', 'nome', 'valor_nome'
    ];
}
