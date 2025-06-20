<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoAgendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'token_whatsapp', 'tempo_descanso_entre_agendamento', 'msg_wpp_manha', 'msg_wpp_manha_horario',
        'msg_wpp_alerta', 'msg_wpp_alerta_minutos_antecedencia', 'mensagem_manha', 'mensagem_alerta'
    ];
}
