<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratorio extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nome', 'cnpj', 'email', 'telefone', 'cidade_id', 'rua', 'numero', 'bairro', 'status', 'cep'
    ];

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }
}
