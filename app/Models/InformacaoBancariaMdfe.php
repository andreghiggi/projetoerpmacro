<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformacaoBancariaMdfe extends Model
{
    use HasFactory;

    protected $fillable = [
        'mdfe_id', 'codigo_banco', 'codigo_agencia', 'cnpj_ipef'
    ];
}
