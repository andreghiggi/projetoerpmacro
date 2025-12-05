<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'fornecedor_id', 'responsavel', 'hash_link', 'referencia', 'observacao', 'estado',
        'status', 'valor_total', 'desconto', 'planejamento_id'
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function fornecedor(){
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    public function planejamento(){
        return $this->belongsTo(PlanejamentoCusto::class, 'planejamento_id');
    }

    public function itens(){
        return $this->hasMany(ItemCotacao::class, 'cotacao_id')->with('produto');
    }

    public function fatura(){
        return $this->hasMany(FaturaCotacao::class, 'cotacao_id');
    }
}
