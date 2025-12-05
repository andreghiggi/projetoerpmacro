<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NaturezaOperacao extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'empresa_id', 'descricao', 'cst_csosn', 'cst_pis', 'cst_cofins', 'cst_ipi',
        'cfop_estadual', 'cfop_outro_estado', 'cfop_entrada_estadual', 'cfop_entrada_outro_estado', 'perc_icms', 'perc_pis',
        'perc_cofins', 'perc_ipi', 'padrao', 'sobrescrever_cfop', '_id_import', 'movimentar_estoque'
    ];
}
