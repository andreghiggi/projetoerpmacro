<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdvLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'usuario_id', 'produto_id', 'acao', 'valor_desconto', 'valor_acrescimo'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
