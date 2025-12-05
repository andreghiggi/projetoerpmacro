<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroTef extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nfce_id', 'nome_rede', 'nsu', 'data_transacao', 'hora_transacao', 'valor_total', 'hash',
        'estado', 'usuario_id', 'finalizacao'
    ];

    public function nfce(){
        return $this->belongsTo(Nfce::class, 'nfce_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function getDataTransacao(){
        return substr($this->data_transacao, 0,2) . "/".
        substr($this->data_transacao, 2,2) . "/".
        substr($this->data_transacao, 4,4);
    }

    public function getHoraTransacao(){
        return substr($this->hora_transacao, 0,2) . ":".
        substr($this->hora_transacao, 2,2) . ":".
        substr($this->hora_transacao, 4,2);
    }

}
