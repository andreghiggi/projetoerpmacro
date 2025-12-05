<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adicional extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nome', 'status', 'valor', 'nome_en', 'nome_es', 'categoria_id'
    ];

    public function categoria(){
        return $this->belongsTo(CategoriaAdicional::class, 'categoria_id');
    }
}
