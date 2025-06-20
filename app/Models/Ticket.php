<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'assunto', 'departamento', 'status', 'empresa_id'
    ];

    public function mensagens()
    {
        return $this->hasMany(TicketMensagem::class, 'ticket_id')->orderBy('id', 'desc');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
