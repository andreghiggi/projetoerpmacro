<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrinhoDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'empresa_id', 'estado', 'valor_total', 'endereco_id', 'valor_frete', 'session_cart_delivery',
        'valor_desconto', 'cupom', 'tipo_entrega', 'fone', 'funcionario_id_agendamento', 'inicio_agendamento',
        'fim_agendamento', 'data_agendamento', 'observacao'
    ];

    public function itens(){
        return $this->hasMany(ItemCarrinhoDelivery::class, 'carrinho_id');
    }

    public function itensProdutos(){
        return $this->hasMany(ItemCarrinhoDelivery::class, 'carrinho_id')->where('produto_id', '!=', null);
    }

    public function itensServico(){
        return $this->hasMany(ItemCarrinhoDelivery::class, 'carrinho_id')->where('servico_id', '!=', null);
    }

    public function endereco(){
        return $this->belongsTo(EnderecoDelivery::class, 'endereco_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
