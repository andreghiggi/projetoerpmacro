<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedidoDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 'produto_id', 'status', 'quantidade', 'observacao', 'tamanho_id', 'valor_unitario', 'sub_total',
        'estado', 'servico_id', 'impresso'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id')->with(['categoria']);
    }

    public function servico(){
        return $this->belongsTo(Servico::class, 'servico_id')->with(['categoria']);
    }

    public function tamanho(){
        return $this->belongsTo(TamanhoPizza::class, 'tamanho_id');
    }

    public function adicionais(){
        return $this->hasMany(ItemAdicionalDelivery::class, 'item_pedido_id')->with('adicional');
    }

    public function pedido(){
        return $this->belongsTo(PedidoDelivery::class, 'pedido_id');
    }

    public function pizzas(){
        return $this->hasMany(ItemPizzaPedidoDelivery::class, 'item_pedido_id');
    }

    public function getAdicionaisStr(){
        $adds = "";
        foreach($this->adicionais as $a){
            $adds .= $a->adicional->nome . ", ";
        }
        $adds = substr($adds, 0, strlen($adds)-2);
        return $adds;
    }
}
