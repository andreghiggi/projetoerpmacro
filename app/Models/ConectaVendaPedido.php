<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConectaVendaPedido extends Model
{
    use HasFactory;

    protected $table = 'conecta_venda_pedidos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'empresa_id', 'conecta_pedido_id' , 'situacao', 'comprador', 'vendedor', 'vendedor_id','nfe_id', 'catalogo', 'tabela', 'email', 'telefone', 'observacao',
        'razao_social', 'inscricao_estadual', 'cpf', 'cnpj', 'cep', 'estado', 'cidade', 'endereco', 'numero', 'complemento',
        'bairro', 'data_criacao', 'indice_catalogo', 'valor_pedido', 'valor_frete', 'frete_tipo', 'cupom', 'desconto', 'valor_desconto',
        'valor_pagamento', 'pagamento_intermediador', 'pagamento_tipo', 'parcelas', 'data_atualizacao_status','cliente_id'
    ];

    public $timestamps = false;

    public function produtos()
    {
        return $this->hasMany(ConectaVendaItemPedido::class, 'pedido_id', 'id');
    }

    public function itens(){
        return $this->hasMany(ConectaVendaItemPedido::class, 'pedido_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function nfe(){
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }
}
