<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceiroContador extends Model
{
    use HasFactory;

    protected $fillable = [
        'contador_id', 'percentual_comissao', 'valor_comissao', 'mes', 'ano', 'tipo_pagamento', 'observacao',
        'total_venda', 'status_pagamento'
    ];

    public function contador(){
        return $this->belongsTo(Empresa::class, 'contador_id');
    }

    public static function tiposPagamento(){
        return [
            'Dinheiro' => 'Dinheiro',
            'Cheque' => 'Cheque',
            'Boleto' => 'Boleto',
            'Depósito Bancário' => 'Depósito Bancário',
            'Pix' => 'Pix',
            'Outros' => 'Outros'
        ];
    }

    public static function meses(){
        return [
            'janeiro' => 'Janeiro',
            'fevereiro' => 'Fevereiro',
            'março' => 'Março',
            'abril' => 'Abril',
            'maio' => 'Maio',
            'junho' => 'Junho',
            'julho' => 'Julho',
            'agosto' => 'Agosto',
            'setembro' => 'Setembro',
            'outubro' => 'Outubro',
            'novembro' => 'Novembro',
            'dezembro' => 'Dezembro',
        ];
    }

    public static function anos(){
        $anos = [];
        $a = date('Y')-4;
        for($i=$a; $i<$a+20; $i++){
            array_push($anos, $i);
        }
        return $anos;
    }
    
}
