<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WoocommerceItemPedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 'produto_id', 'item_id', 'item_nome', 'quantidade', 'valor_unitario', 'sub_total'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

}
