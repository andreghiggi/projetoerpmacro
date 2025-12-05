<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaPreco extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nome', 'ajuste_sobre', 'tipo', 'percentual_alteracao', 'tipo_pagamento', 'funcionario_id', 'status',
        'valor_alteracao'
    ];

    public function itens(){
        return $this->hasMany(ItemListaPreco::class, 'lista_id');
    }

    public function usuarios(){
        return $this->hasMany(ListaPrecoUsuario::class, 'lista_preco_id');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public static function tiposPagamento()
    {
        return [
            '01' => 'Dinheiro',
            '02' => 'Cheque',
            '03' => 'Cartão de Crédito',
            '04' => 'Cartão de Débito',
            '05' => 'Crédito Loja',
            '06' => 'Crediário',
            '10' => 'Vale Alimentação',
            '11' => 'Vale Refeição',
            '12' => 'Vale Presente',
            '13' => 'Vale Combustível',
            '14' => 'Duplicata Mercantil',
            '15' => 'Boleto Bancário',
            '16' => 'Depósito Bancário',
            '17' => 'Pagamento Instantâneo (PIX)',
            '90' => 'Sem Pagamento',
            // '99' => 'Outros',
        ];
    }

    public function getTipoPagamento(){
        return ListaPreco::tiposPagamento()[$this->tipo_pagamento];
    }
}
