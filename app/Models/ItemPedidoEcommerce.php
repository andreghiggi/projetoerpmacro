<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedidoEcommerce extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 'produto_id', 'variacao_id', 'quantidade', 'valor_unitario', 'sub_total'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id')->with(['categoria']);
    }

    public function produtoVariacao(){
        return $this->belongsTo(ProdutoVariacao::class, 'variacao_id');
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
