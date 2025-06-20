<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuracaoMensal extends Model
{
    use HasFactory;

    protected $fillable = [
        'funcionario_id', 'mes', 'ano', 'valor_final', 'forma_pagamento', 'observacao', 
        'conta_pagar_id'
    ];

    public function funcionario(){
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function eventos(){
        return $this->hasMany(ApuracaoMensalEvento::class, 'apuracao_id');
    }

    public function contaPagar(){
        return $this->belongsTo(ContaPagar::class, 'conta_pagar_id');
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

    public static function mesesApuracao(){
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

    public static function anosApuracao(){
        $anos = [];
        $a = date('Y');
        for($i=$a; $i<$a+20; $i++){
            array_push($anos, $i);
        }
        return $anos;
    }
}
