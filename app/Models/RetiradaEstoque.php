<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetiradaEstoque extends Model
{
    use HasFactory;

    protected $fillable = [
        'motivo', 'observacao', 'produto_id', 'empresa_id', 'quantidade', 'local_id'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public static function motivos(){
        return [
            'Avarias' => 'Avarias', 
            'Perdas' => 'Perdas', 
            'Brindes' => 'Brindes'
        ];
    }
}
