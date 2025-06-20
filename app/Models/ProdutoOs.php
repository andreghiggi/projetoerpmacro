<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoOs extends Model
{
    use HasFactory;

    protected $fillable = [
    	'produto_id', 'ordem_servico_id', 'quantidade', 'valor', 'subtotal'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function ordemServico(){
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id');
    }
}
