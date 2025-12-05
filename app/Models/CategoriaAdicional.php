<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaAdicional extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nome', 'minimo_escolha', 'maximo_escolha', 'status'
    ];

    public function adicionais(){
        return $this->hasMany(Adicional::class, 'categoria_id')->where('status', 1);
    }
}
