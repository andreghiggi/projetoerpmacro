<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAgendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'agendamento_id', 'servico_id', 'quantidade', 'valor'
    ];

    public function servico(){
        return $this->belongsTo(Servico::class, 'servico_id');
    }
}
