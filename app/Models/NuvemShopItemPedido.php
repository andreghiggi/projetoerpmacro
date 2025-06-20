<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NuvemShopItemPedido extends Model
{
    use HasFactory;

    protected $fillable = [ 'pedido_id', 'produto_id', 'quantidade', 'sub_total', 'nome', 'valor_unitario' ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
