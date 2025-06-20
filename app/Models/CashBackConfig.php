<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBackConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'valor_percentual', 'dias_expiracao', 'valor_minimo_venda', 'percentual_maximo_venda',
        'mensagem_padrao_whatsapp', 'mensagem_automatica_5_dias', 'mensagem_automatica_1_dia'
    ];
}
