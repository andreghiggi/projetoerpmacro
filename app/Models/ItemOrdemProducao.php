<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemOrdemProducao extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordem_producao_id', 'item_producao_id', 'produto_id', 'quantidade', 'status', 'observacao', 'cliente_id',
        'numero_pedido'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function itemProducao(){
        return $this->belongsTo(ItemProducao::class, 'item_producao_id');
    }
}
