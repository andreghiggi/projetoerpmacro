<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotoboyComissao extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'pedido_id', 'valor', 'valor_total_pedido', 'status', 'motoboy_id'
    ];

    public function pedido()
    {
        return $this->belongsTo(PedidoDelivery::class, 'pedido_id');
    }

    public function motoboy()
    {
        return $this->belongsTo(Motoboy::class, 'motoboy_id');
    }
}
