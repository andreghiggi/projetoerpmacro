<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'cliente_id', 'acomodacao_id', 'data_checkin', 'data_checkout', 'valor_estadia',
        'desconto', 'valor_outros', 'valor_total', 'estado', 'observacao', 'conferencia_frigobar',
        'total_hospedes', 'codigo_reseva', 'link_externo', 'numero_sequencial'
    ];

    public function consumoProdutos(){
        return $this->hasMany(ConsumoReserva::class, 'reserva_id');
    }

    public function consumoServicos(){
        return $this->hasMany(ServicoReserva::class, 'reserva_id');
    }

    public function notas(){
        return $this->hasMany(NotasReserva::class, 'reserva_id');
    }

    public function hospedes(){
        return $this->hasMany(HospedeReserva::class, 'reserva_id');
    }

    public function fatura(){
        return $this->hasMany(FaturaReserva::class, 'reserva_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function acomodacao(){
        return $this->belongsTo(Acomodacao::class, 'acomodacao_id');
    }

    public function colorStatus(){
        if($this->estado == 'iniciado') return 'success';
        elseif($this->estado == 'pendente') return 'warning';
        elseif($this->estado == 'finalizado') return 'primary';
        return 'danger';
    }

}
