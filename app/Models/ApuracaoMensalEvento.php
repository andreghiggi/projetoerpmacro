<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuracaoMensalEvento extends Model
{
    use HasFactory;

    protected $fillable = [
        'apuracao_id', 'evento_id', 'valor', 'metodo', 'condicao', 'nome'
    ];

    public function evento()
    {
        return $this->belongsTo(EventoSalario::class, 'evento_id');
    }
}
