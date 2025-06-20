<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoUnico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nfe_id', 'nfce_id', 'produto_id', 'codigo', 'observacao', 'tipo', 'em_estoque'
    ];

    public function nfe(){
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }

    public function nfce(){
        return $this->belongsTo(Nfce::class, 'nfce_id');
    }

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
