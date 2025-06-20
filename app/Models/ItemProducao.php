<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemProducao extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'quantidade', 'status', 'item_id', 'observacao', 'dimensao'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function itemNfe(){
        return $this->belongsTo(ItemNfe::class, 'item_id');
    }
}
