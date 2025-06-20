<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxaPagamento extends Model
{
    use HasFactory;

    protected $fillable = [ 'empresa_id', 'taxa', 'tipo_pagamento', 'bandeira_cartao' ];

    public static function tiposPagamento(){
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
            '99' => 'Outros',
        ];
    }

    public function getTipo(){
        $tipos = TaxaPagamento::tiposPagamento();
        return $tipos[$this->tipo_pagamento];
    }

    public static function bandeiras(){
        return [
            '01' => 'Visa',
            '02' => 'Mastercard',
            '03' => 'American Express',
            '04' => 'Sorocred',
            '05' => 'Diners Club',
            '06' => 'Elo',
            '07' => 'Hipercard',
            '08' => 'Aura',
            '09' => 'Cabal',
            '99' => 'Outros'
        ];
    }

    public function getBandeira(){
        $bandeiras = TaxaPagamento::bandeiras();
        return $bandeiras[$this->bandeira_cartao];
    }
}