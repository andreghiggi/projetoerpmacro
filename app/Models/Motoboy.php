<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motoboy extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'telefone', 'valor_comissao', 'tipo_comissao', 'status', 'empresa_id'
    ];

    public function getInfoAttribute()
    {
        return "$this->nome $this->telefone";
    }
}
