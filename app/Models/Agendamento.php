<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'funcionario_id', 'cliente_id', 'data', 'inicio', 'termino', 'observacao', 'total',
        'desconto', 'acrescimo', 'status', 'empresa_id', 'prioridade', 'pedido_delivery_id',
        'numero_sequencial'
    ];

    public function itens(){
        return $this->hasMany(ItemAgendamento::class, 'agendamento_id', 'id')->with('servico');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function pedidoDelivery(){
        return $this->belongsTo(PedidoDelivery::class, 'pedido_delivery_id');
    }

    public function funcionario(){
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function getPrioridade(){
        if($this->status) return 'bg-success';
        if($this->prioridade == 'baixa') return 'bg-primary';
        if($this->prioridade == 'media') return 'bg-warning';
        return 'bg-danger';
    }
}
