<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaturaOrdemServico extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordem_servico_id', 'tipo_pagamento', 'data_vencimento', 'valor'
    ];

    public function getTipoPagamento()
    {
        foreach (Nfe::tiposPagamento() as $key => $t) {
            if ($this->tipo_pagamento == $key) return $t;
        }
    }
}
