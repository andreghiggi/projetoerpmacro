<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBackCliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'cliente_id', 'tipo', 'venda_id', 'valor_venda', 'valor_credito', 'valor_percentual',
        'status', 'data_expiracao'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
