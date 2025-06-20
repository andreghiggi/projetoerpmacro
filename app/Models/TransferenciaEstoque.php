<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferenciaEstoque extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'local_saida_id', 'local_entrada_id', 'usuario_id', 'observacao', 'codigo_transacao'
    ];

    public function local_saida(){
        return $this->belongsTo(Localizacao::class, 'local_saida_id');
    }

    public function local_entrada(){
        return $this->belongsTo(Localizacao::class, 'local_entrada_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function itens(){
        return $this->hasMany(ItemTransferenciaEstoque::class, 'transferencia_id');
    }
}
