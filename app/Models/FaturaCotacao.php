<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaturaCotacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'cotacao_id', 'tipo_pagamento', 'data_vencimento', 'valor'
    ];

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
        ];
    }

    public function getTipoPagamento()
    {
        if (isset(FaturaCotacao::tiposPagamento()[$this->tipo_pagamento])) {
            return FaturaCotacao::tiposPagamento()[$this->tipo_pagamento];
        } else {
            return "Não identificado";
        }
    }
}
