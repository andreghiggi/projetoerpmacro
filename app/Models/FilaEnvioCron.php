<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilaEnvioCron extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'mensagem', 'enviado_em', 'status', 'erro', 'enviar_whatsapp', 'enviar_email', 
        'whatsapp', 'email', 'agendar_para', 'cliente_id', 'tipo'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function _tipo(){
        return MensagemPadraoCrm::tipos()[$this->tipo];
    }
}
