<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inutilizacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'numero_inicial', 'numero_final', 'numero_serie', 'modelo',
        'justificativa'
    ];
}
