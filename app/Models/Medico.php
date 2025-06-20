<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nome', 'cpf', 'email', 'telefone', 'cidade_id', 'rua', 'numero', 'bairro', 'status', 'crm', 'cep'
    ];

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

}
