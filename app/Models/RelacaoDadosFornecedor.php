<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelacaoDadosFornecedor extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'cst_csosn_entrada', 'cfop_entrada', 'cst_csosn_saida', 'cfop_saida'
    ];

}
