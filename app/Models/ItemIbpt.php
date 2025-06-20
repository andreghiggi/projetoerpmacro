<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemIbpt extends Model
{
    use HasFactory;

    protected $fillable = [
        'ibpt_id', 'codigo', 'descricao', 'nacional_federal', 'importado_federal', 'estadual',
        'municipal'
    ];
}
