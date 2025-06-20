<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'razao_social', 'cpf_cnpj', 'rua', 'numero', 'bairro', 'cidade_id', 'cep', 'telefone',
        'email', 'complemento', 'empresa_id', 'horario_checkin', 'horario_checkout'
    ];

    public function getEnderecoAttribute()
    {
        return "$this->rua, $this->numero - $this->bairro " . $this->cidade->info;
    }

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }
}
