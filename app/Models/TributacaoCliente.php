<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TributacaoCliente extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'perc_icms', 'perc_pis', 'perc_cofins', 'perc_ipi', 'cst_csosn', 'cst_pis', 'cst_cofins', 'cst_ipi', 'perc_red_bc',
        'cliente_id', 'cfop_estadual', 'cfop_outro_estado', 'cest', 'ncm', 'codigo_beneficio_fiscal'
    ];

    public function _ncm(){
        return $this->belongsTo(Ncm::class, 'ncm', 'codigo');
    }
}
