<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuncionarioEvento extends Model
{
    use HasFactory;

    protected $fillable = [
        'evento_id', 'funcionario_id', 'condicao', 'metodo', 'valor', 'ativo'
    ];

    public function evento(){
        return $this->belongsTo(EventoSalario::class, 'evento_id');
    }

    public function funcionario(){
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }
}
