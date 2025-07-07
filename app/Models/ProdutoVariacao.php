<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoVariacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'descricao', 'valor', 'codigo_barras', 'referencia', 'imagem'
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

    public function getImgAppAttribute()
    {
        if($this->imagem == ""){
            return env("APP_URL") . "/imgs/no-image.png";
        }
        return env("APP_URL") . "/uploads/produtos/$this->imagem";
    }
    public function getImgAttribute()
    {
        if($this->imagem == ""){
            return "/imgs/no-image.png";
        }
        return "/uploads/produtos/$this->imagem";
    }

}
