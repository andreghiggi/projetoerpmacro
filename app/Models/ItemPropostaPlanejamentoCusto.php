<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPropostaPlanejamentoCusto extends Model
{
    use HasFactory;

    protected $fillable = [
        'planejamento_id', 'descricao', 'quantidade', 'valor_unitario_custo', 'valor_unitario_final', 'sub_total_custo',
        'sub_total_final', 'tipo', 'observacao', 'servico_id', 'produto_id', 'terceiro',
        'espessura', 'largura', 'comprimento', 'peso_especifico', 'calculo', 'peso_bruto'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function descricao(){
        $descricao = $this->produto->nome;

        if($this->largura > 0){
            $descricao .= " L = $this->largura";
        }
        if($this->comprimento > 0){
            $descricao .= " C = $this->comprimento";
        }
        if($this->espessura > 0){
            $descricao .= " E = $this->espessura";
        }

        return $descricao;
    }
}
