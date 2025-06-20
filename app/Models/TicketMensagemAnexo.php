<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMensagemAnexo extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_mensagem_id', 'anexo'
    ];

    public function getFileAttribute()
    {
        return "/uploads/ticket/$this->anexo";
    }
}
