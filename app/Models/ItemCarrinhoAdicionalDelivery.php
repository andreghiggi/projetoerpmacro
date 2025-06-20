<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCarrinhoAdicionalDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_carrinho_id', 'adicional_id'
    ];

    public function adicional(){
        return $this->belongsTo(Adicional::class, 'adicional_id');
    }
}
