<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemInventarioImpressao extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventario_id', 'produto_id', 'quantidade', 'observacao', 'estado', 'usuario_id'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function inventario(){
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }
}
