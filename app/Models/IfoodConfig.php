<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IfoodConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'clientId', 'clientSecret', 'grantType', 'authorizationCode', 'authorizationCodeVerifier',
        'accessToken', 'refreshToken', 'verificationUrlComplete', 'userCode', 'merchantId', 'catalogId', 'merchantUUID'
    ];

    public static function getStatusErros(){
        return [
            '501' => 'PROBLEMAS DE SISTEMA',
            '502' => 'PEDIDO EM DUPLICIDADE',
            '503' => 'ITEM INDISPONÍVEL',
            '504' => 'RESTAURANTE SEM MOTOBOY',
            '505' => 'CARDÁPIO DESATUALIZADO',
            '506' => 'PEDIDO FORA DA ÁREA DE ENTREGA',
            '507' => 'CLIENTE GOLPISTA/TROTE',
            '508' => 'FORA DO HORÁRIO DO DELIVERY',
            '509' => 'DIFICULDADES INTERNAS DO RESTAURANTE',
            '511' => 'ÁREA DE RISCO',
            '512' => 'RESTAURANTE ABRIRÁ MAIS TARDE'
        ];
    }
}
