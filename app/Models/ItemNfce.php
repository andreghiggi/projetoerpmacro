<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemNfce extends Model
{
    use HasFactory;

    protected $fillable = [
        'nfce_id', 'produto_id', 'quantidade', 'valor_unitario', 'sub_total', 'perc_icms', 'perc_pis',
        'perc_cofins', 'perc_ipi', 'cst_csosn', 'cst_pis', 'cst_cofins', 'cst_ipi', 'perc_red_bc', 'cfop',
        'ncm', 'origem', 'cEnq', 'pST', 'vBCSTRet', 'cest', 'codigo_beneficio_fiscal', 'variacao_id', 'tamanho_id', 'observacao'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function produtoVariacao(){
        return $this->belongsTo(ProdutoVariacao::class, 'variacao_id');
    }

    public function adicionais(){
        return $this->hasMany(ItemAdicionalNfce::class, 'item_nfce_id')->with('adicional');
    }

    public function pizzas(){
        return $this->hasMany(ItemPizzaNfce::class, 'item_nfce_id')->with('sabor');
    }

    public function tamanho(){
        return $this->belongsTo(TamanhoPizza::class, 'tamanho_id');
    }

    public function descricao(){

        $descricao = $this->produto->nome;
        
        if($this->produtoVariacao){
            $descricao .= " - " . $this->produtoVariacao->descricao;
        }

        if(sizeof($this->adicionais) > 0){
            $adicionalStr = " - adicionais: ";
            foreach($this->adicionais as $a){
                $adicionalStr .= $a->adicional->nome . ", ";
            }
            $adicionalStr = substr($adicionalStr, 0, strlen($adicionalStr)-2);
            $descricao .= $adicionalStr;
        }

        if($this->tamanho){
            $descricao .= " - tamanho: " . $this->tamanho->nome;
        }

        if($this->observacao){
            $descricao .= " - observação: " . $this->observacao;
        }
        return $descricao;
    }

    // public function descricao(){
    //     if($this->variacao_id == null){
    //         return $this->produto->nome;
    //     }
    //     if($this->produtoVariacao){
    //         return $this->produto->nome . " - " . $this->produtoVariacao->descricao;
    //     }
    //     return $this->produto->nome;
    // }
}
