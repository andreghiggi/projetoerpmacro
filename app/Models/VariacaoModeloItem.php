<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariacaoModeloItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'variacao_modelo_id', 'nome', 'vendizap_id'
    ];

    public function variacaoModelo(){
        return $this->belongsTo(VariacaoModelo::class, 'variacao_modelo_id');
    }


}
