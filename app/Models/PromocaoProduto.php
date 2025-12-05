<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromocaoProduto extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'status', 'valor', 'data_inicio', 'data_fim', 'valor_original'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
    
}
