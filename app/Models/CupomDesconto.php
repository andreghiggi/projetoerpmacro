<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CupomDesconto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo', 'valor', 'tipo_desconto', 'cliente_id', 'status', 'empresa_id',
        'valor_minimo_pedido', 'descricao', 'expiracao'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
