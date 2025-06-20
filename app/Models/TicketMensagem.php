<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMensagem extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'descricao', 'resposta'
    ];

    public function anexos()
    {
        return $this->hasMany(TicketMensagemAnexo::class, 'ticket_mensagem_id');
    }
}
