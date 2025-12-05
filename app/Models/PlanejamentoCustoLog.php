<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanejamentoCustoLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'planejamento_id', 'usuario_id', 'estado_anterior', 'estado_alterado', 'observacao'
    ];

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function _estadoAnterior(){

        if($this->estado_anterior == 'novo'){
            return "<h5 class='badge bg-light text-dark'>NOVO</h5>";
        }else if($this->estado_anterior == 'proposta'){
            return "<h5 class='badge bg-primary'>PROPOSTA</h5>";
        }else if($this->estado_anterior == 'cotacao'){
            return "<h5 class='badge bg-info'>COTAÇÃO</h5>";
        }else if($this->estado_anterior == 'producao'){
            return "<h5 class='badge bg-dark'>PRODUÇÃO</h5>";
        }else if($this->estado_anterior == 'cancelado'){
            return "<h5 class='badge bg-danger'>CANCELADO</h5>";
        }else{
            return "<h5 class='badge bg-success'>FINALIZADO</h5>";
        }
    }

    public function _estadoAlterado(){
        if($this->estado_alterado == 'novo'){
            return "<h5 class='badge bg-light text-dark'>NOVO</h5>";
        }else if($this->estado_alterado == 'proposta'){
            return "<h5 class='badge bg-primary'>PROPOSTA</h5>";
        }else if($this->estado_alterado == 'cotacao'){
            return "<h5 class='badge bg-info'>COTAÇÃO</h5>";
        }else if($this->estado_alterado == 'producao'){
            return "<h5 class='badge bg-dark'>PRODUÇÃO</h5>";
        }else if($this->estado_alterado == 'cancelado'){
            return "<h5 class='badge bg-danger'>CANCELADO</h5>";
        }else{
            return "<h5 class='badge bg-success'>FINALIZADO</h5>";
        }
    }
}
