<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MensagemPadraoCrm extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'mensagem', 'status', 'tipo', 'enviar_whatsapp', 'enviar_email', 'horario_envio', 'dias_apos_venda',
        'titulo', 'dias_ultima_venda', 'mensagem_para_agendamento', 'dias_apos_agendamento'
    ];

    public static function tipos(){
        return [
            'pos_venda' => 'Pós venda',
            'aniversario' => 'Aniversário',
            'reativacao' => 'Reativação',
            'pos_agendamento' => 'Pós agendamento',
        ];
    }

    public function _tipo(){
        return $this->tipos()[$this->tipo];
    }
}
