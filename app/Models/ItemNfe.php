<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemNfe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nfe_id', 'produto_id', 'quantidade', 'valor_unitario', 'sub_total', 'perc_icms', 'perc_pis',
        'perc_cofins', 'perc_ipi', 'cst_csosn', 'cst_pis', 'cst_cofins', 'cst_ipi', 'perc_red_bc', 'cfop',
        'ncm', 'cEnq', 'pST', 'vBCSTRet', 'origem', 'cest', 'codigo_beneficio_fiscal', 'valor_custo', 'lote',
        'data_vencimento', 'variacao_id', 'vbc_icms', 'vbc_pis', 'vbc_cofins', 'vbc_ipi', 'xPed', 'nItemPed',
        'infAdProd', 'pMVAST', 'vBCST', 'pICMSST', 'vICMSST', 'vBCFCPST', 'pFCPST', 'vFCPST', 'modBCST'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function nfe(){
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }

    public function produtoVariacao(){
        return $this->belongsTo(ProdutoVariacao::class, 'variacao_id');
    }

    public function itensDimensao(){
        return $this->hasMany(ItemDimensaoNfe::class, 'item_nfe_id');
    }

    public function descricao(){
        if($this->variacao_id == null){
            return $this->produto->nome;
        }
        if($this->produtoVariacao){
            return $this->produto->nome . " - " . $this->produtoVariacao->descricao;
        }

        return $this->produto->nome;
    }
}
