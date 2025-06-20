<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuncionamentoDelivery extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'inicio', 'fim', 'dia', 'empresa_id'
    ];

    public static function getDiaSemana(){
        return [
            'domingo' => 'Domingo',
            'segunda' => 'Segunda-feira',
            'terca' => 'Terça-feira', 
            'quarta' => 'Quarta-feira', 
            'quinta' => 'Quinta-feira', 
            'sexta' => 'Sexta-feira',
            'sabado' => 'Sabado',
        ];
    }

    public static function getDia($indice){
        $d = [
            'domingo',
            'segunda',
            'terca', 
            'quarta', 
            'quinta', 
            'sexta',
            'sabado',
        ];
        return $d[$indice];
    }

    public function getInicioParseAttribute()
    {
        return \Carbon\Carbon::parse($this->inicio)->format('H:i');
    }

    public function getFinalParseAttribute()
    {
        return \Carbon\Carbon::parse($this->fim)->format('H:i');
    }

    public function getDiaStr(){
        $dias = DiaSemana::getDias();
        return $dias[$this->dia];
    }
}
