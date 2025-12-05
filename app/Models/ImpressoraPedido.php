<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpressoraPedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'descricao', 'status', 'requisicao_segundos'
    ];

    public function produtos()
    {
        return $this->hasMany(ImpressoraPedidoProduto::class, 'impressora_id');
    }
}
