<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PadraoTributacaoProduto extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'perc_icms', 'perc_pis', 'perc_cofins', 'perc_ipi', 'cst_csosn', 'cst_pis', 'cst_cofins', 'cst_ipi', 'perc_red_bc', 
        'cEnq', 'pST', 'empresa_id', 'descricao', 'cfop_estadual', 'cfop_outro_estado', 'cest', 'ncm', 
        'codigo_beneficio_fiscal', 'cfop_entrada_estadual', 'cfop_entrada_outro_estado', 'padrao', 'modBCST', 'pMVAST', 'pICMSST', 
        'redBCST', 'cst_ibscbs', 'cclass_trib', 'perc_ibs_uf', 'perc_ibs_mun', 'perc_cbs', 'perc_dif'
    ];

    public function _ncm(){
        return $this->belongsTo(Ncm::class, 'ncm', 'codigo');
    }
}
