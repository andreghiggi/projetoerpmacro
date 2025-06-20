<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoEcommerce extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'empresa_id', 'endereco_id', 'estado', 'valor_total', 'valor_frete', 'tipo_frete',
        'rua_entrega', 'numero_entrega', 'referencia_entrega', 'bairro_entrega', 'cep_entrega', 
        'hash_pedido', 'cupom_desconto', 'observacao', 'desconto', 'data_entrega', 'codigo_rastreamento',
        'cidade_entrega', 'nome', 'sobre_nome', 'email', 'tipo_documento', 'numero_documento', 'link_boleto', 
        'qr_code_base64', 'qr_code', 'status_pagamento'
    ];

    public function getEnderecoAttribute()
    {
        return "$this->rua_entrega, $this->numero_entrega - $this->bairro_entrega - $this->cidade_entrega";
    }

    public function itens(){
        return $this->hasMany(ItemPedidoEcommerce::class, 'pedido_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function _estado(){
        if($this->estado == 'novo'){
            return "<h5 class='text-dark'>NOVO</h5>";
        }else if($this->estado == 'preparando'){
            return "<h5 class='text-warning'>PREPARANDO</h5>";
        }else if($this->estado == 'em_trasporte'){
            return "<h5 class='text-primary'>EM TRANSPORTE</h5>";
        }else if($this->estado == 'finalizado'){
            return "<h5 class='text-success'>FINALIZADO</h5>";
        }else{
            return "<h5 class='text-danger'>RECUSADO</h5>";
        }
    }

    public static function estados(){
        return [
            'novo' => 'Novo',
            'aprovado' => 'Aprovado',
            'em_trasporte' => 'Em transporte',
            'finalizado' => 'Finalizado',
            'recusado' => 'Recusado'
        ];
    }

}
