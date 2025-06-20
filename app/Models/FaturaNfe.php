<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaturaNfe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nfe_id', 'tipo_pagamento', 'data_vencimento', 'valor'
    ];

    public function nfe()
    {
        return $this->belongsTo(Nfe::class, 'nfe_id');
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

    public function getTipoPagamento()
    {
        foreach (Nfe::tiposPagamento() as $key => $t) {
            if ($this->tipo_pagamento == $key) return $t;
        }
    }

    public static function getTipo($tipo)
    {
        if (isset(Nfe::tiposPagamento()[$tipo])) {
            return Nfe::tiposPagamento()[$tipo];
        } else {
            return "Não identificado";
        }
        // $tipos = Venda::tiposPagamento();
        // return $tipos[$tipo];
    }
}
