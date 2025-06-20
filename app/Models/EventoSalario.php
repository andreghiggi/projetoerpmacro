<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoSalario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'tipo', 'metodo', 'condicao', 'ativo', 'empresa_id', 'tipo_valor'
    ];
}
