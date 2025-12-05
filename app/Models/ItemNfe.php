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
        'infAdProd', 'pMVAST', 'vBCST', 'pICMSST', 'vICMSST', 'vBCFCPST', 'pFCPST', 'vFCPST', 'modBCST',
        'nDI', 'dDI', 'cidade_desembarque_id', 'dDesemb', 'tpViaTransp', 'vAFRMM', 'tpIntermedio', 'cpf_cnpj_di',
        'UFTerceiro', 'cExportador', 'nAdicao', 'cFabricante', 'vBCII', 'vDespAdu', 'vII', 'vIOF', 'descricao',
        'vICMSSubstituto'
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

    public function cidadeDesembarque(){
        return $this->belongsTo(Cidade::class, 'cidade_desembarque_id');
    }

    public function descricao(){

        if(strlen($this->descricao) > 1){
            return $this->descricao;
        }
        if($this->variacao_id == null){
            return $this->produto->nome;
        }
        if($this->produtoVariacao){
            return $this->produto->nome . " - " . $this->produtoVariacao->descricao;
        }

        return $this->produto->nome;
    }

    public function __statusVencimento(){
        if(\Carbon\Carbon::parse($this->data_vencimento)->isPast()){
            return '<span class="badge bg-danger">VENCIDO</span>'; 
        }
        return '<span class="badge bg-success">EM DIA</span>'; 
    }

    public function dadosImportacaoEdit(){
        $dados = [
            'nDI' => $this->nDI,
            'dDI' => $this->dDI,
            'cidade_desembarque_id' => $this->cidade_desembarque_id,
            'dDesemb' => $this->dDesemb,
            'tpViaTransp' => $this->tpViaTransp,
            'vAFRMM' => __moeda($this->vAFRMM),
            'tpIntermedio' => $this->tpIntermedio,
            'cpf_cnpj_di' => $this->cpf_cnpj_di,
            'UFTerceiro' => $this->UFTerceiro,
            'cExportador' => $this->cExportador,
            'nAdicao' => $this->nAdicao,
            'cFabricante' => $this->cFabricante,
            'vBCII' => __moeda($this->vBCII),
            'vDespAdu' => __moeda($this->vDespAdu),
            'vII' => __moeda($this->vII),
            'vIOF' => __moeda($this->vIOF)
        ];
        return json_encode($dados);
    }
}
