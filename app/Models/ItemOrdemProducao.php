<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemOrdemProducao extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordem_producao_id', 'item_producao_id', 'produto_id', 'quantidade', 'status', 'observacao'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function itemProducao(){
        return $this->belongsTo(ItemProducao::class, 'item_producao_id');
    }
}
