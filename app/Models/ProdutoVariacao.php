<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoVariacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'descricao', 'valor', 'codigo_barras', 'referencia', 'imagem', 'variacao_modelo_item_id'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function estoque(){
        return $this->hasOne(Estoque::class, 'produto_variacao_id');
    }

    public function movimentacaoProduto(){
        return $this->hasOne(MovimentacaoProduto::class, 'produto_variacao_id');
    }
    
    public function getImgAttribute()
    {
        if($this->imagem == ""){
            return "/imgs/no-image.png";
        }
        return "/uploads/produtos/$this->imagem";
    }

    public function estoqueNegativo(){
        // dd($this->produto->estoque);
        if($this->produto->gerenciar_estoque == 0) return 0;
        if(!isset($this->estoque) || $this->estoque->quantidade <= 0) return 1;
        return 0;
    }

}
