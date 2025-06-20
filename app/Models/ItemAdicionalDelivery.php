<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAdicionalDelivery extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'item_pedido_id', 'adicional_id'
    ];

    public function adicional(){
        return $this->belongsTo(Adicional::class, 'adicional_id');
    }
}
