<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoEmpresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'assinado', 'data_assinatura', 'cpf_cnpj', 'texto'
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
