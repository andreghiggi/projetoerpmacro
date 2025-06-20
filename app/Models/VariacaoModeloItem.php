<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariacaoModeloItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'variacao_modelo_id', 'nome'
    ];
}
