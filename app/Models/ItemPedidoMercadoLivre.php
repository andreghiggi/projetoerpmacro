<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedidoMercadoLivre extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 'produto_id', 'item_id', 'item_nome', 'condicao', 'variacao_id', 'quantidade',
        'valor_unitario', 'sub_total', 'taxa_venda'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
