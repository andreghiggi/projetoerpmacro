<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interrupcoes extends Model
{
    use HasFactory;

    protected $fillable = [
        'funcionario_id', 'inicio', 'fim', 'dia_id', 'empresa_id', 'motivo', 'status'
    ];

    public function getInicioParseAttribute()
    {
        return \Carbon\Carbon::parse($this->inicio)->format('H:i');
    }

    public function getFinalParseAttribute()
    {
        return \Carbon\Carbon::parse($this->fim)->format('H:i');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function diaSemana()
    {
        return $this->belongsTo(DiaSemana::class, 'dia_id');
    }

    public static function getDias()
    {
        return [
            'segunda' => 'Segunda-feira',
            'terca' => 'Terça-feira',
            'quarta' => 'Quarta-feira',
            'quinta' => 'Quinta-feira',
            'sexta' => 'Sexta-feira',
            'sabado' => 'Sabado',
            'domingo' => 'Domingo'
        ];
    }

    public static function getDiaStr($dia)
    {
        $dias = DiaSemana::getDias();
        return $dias[$dia];
    }
}
