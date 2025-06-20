<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoEmpresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'plano_id', 'data_expiracao', 'valor', 'forma_pagamento'
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function plano(){
        return $this->belongsTo(Plano::class, 'plano_id');
    }


}
