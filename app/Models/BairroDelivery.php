<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BairroDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'bairro_delivery_super', 'valor_entrega', 'empresa_id', 'status'
    ];

    public function getInfoAttribute()
    {
        return "$this->nome - R$ " . __moeda($this->valor_entrega);
    }

}
