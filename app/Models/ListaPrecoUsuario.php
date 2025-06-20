<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaPrecoUsuario extends Model
{
    use HasFactory;

    protected $fillable = [
        'lista_preco_id', 'usuario_id'
    ];
}
