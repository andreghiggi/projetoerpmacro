<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConectaVendaItemPedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 'produto_id', 'variacao_id', 'nome', 'referencia', 'descricao','ean','peso', 'quantidade',
        'valor_unitario', 'observacao', 'sub_total'
    ];


    public function variacoes()
    {
        return $this->belongsTo(ProdutoVariacao::class, 'variacao_id');
    }

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id', 'conecta_venda_id');
    }
}
