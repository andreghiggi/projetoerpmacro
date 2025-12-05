<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedidoVendiZap extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 'produto_id', 'vendizap_produto_id', 'descricao', 'detalhes', 'unidade', 'valor', 'quantidade', 'sub_total', 
        'valor_adicionais', 'observacao', 'codigo', 'valor_promociconal'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function pedido(){
        return $this->belongsTo(PedidoVendiZap::class, 'pedido_id');
    }

    public function getCfop(){
        $uf = $this->pedido->cliente->cidade->uf;
        $empresa = Empresa::findOrFail($this->pedido->empresa_id);

        if($uf == $empresa->cidade->uf){
            return $this->produto->cfop_estadual;
        }
        return $this->produto->cfop_outro_estado;
    }

}
