<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumoReserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'reserva_id', 'produto_id', 'quantidade', 'valor_unitario', 'sub_total', 'observacao', 'frigobar'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
