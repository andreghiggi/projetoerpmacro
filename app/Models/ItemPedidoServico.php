<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedidoServico extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 'servico_id', 'observacao', 'estado', 'quantidade', 'valor_unitario', 'sub_total', 
    ];

    public function servico(){
        return $this->belongsTo(Servico::class, 'servico_id');
    }

    public function pedido(){
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
    
}
