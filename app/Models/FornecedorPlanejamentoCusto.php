<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FornecedorPlanejamentoCusto extends Model
{
    use HasFactory;

    protected $fillable = [
        'planejamento_id', 'fornecedor_id'
    ];

    public function fornecedor(){
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }
}
