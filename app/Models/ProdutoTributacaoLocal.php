<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoTributacaoLocal extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'local_id', 'ncm', 'perc_icms', 'perc_pis', 'perc_cofins', 'perc_ipi', 'cest', 'origem',
        'cst_csosn', 'cst_pis', 'cst_cofins', 'cst_ipi', 'valor_unitario', 'cfop_estadual', 'cfop_outro_estado'
    ];  
}
