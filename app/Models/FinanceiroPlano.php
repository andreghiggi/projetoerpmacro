<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceiroPlano extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'plano_id', 'valor', 'tipo_pagamento', 'status_pagamento', 'plano_empresa_id'
    ];

    public static function statusDePagamentos(){
        return [
            'pendente' => 'Pendente',
            'recebido' => 'Recebido',
            'cancelado' => 'Cancelado'
        ];
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class, 'plano_id');
    }

}
