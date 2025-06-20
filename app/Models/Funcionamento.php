<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionamento extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'inicio', 'fim', 'dia_id', 'funcionario_id'
    ];
    
    public function diaSemana(){
        return $this->belongsTo(DiaSemana::class, 'dia_id');
    }

    public function funcionario(){
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function getInicioParseAttribute()
    {
        return \Carbon\Carbon::parse($this->inicio)->format('H:i');
    }

    public function getFinalParseAttribute()
    {
        return \Carbon\Carbon::parse($this->fim)->format('H:i');
    }

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

    public function getDiaStr(){
        $dias = DiaSemana::getDias();
        return $dias[$this->dia_id];
    }

}
