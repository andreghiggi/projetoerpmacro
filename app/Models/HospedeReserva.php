<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospedeReserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'reserva_id', 'nome_completo', 'cpf', 'rua', 'numero', 'bairro', 'cidade_id', 'telefone',
        'cep', 'email', 'status'
    ];

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }
}
