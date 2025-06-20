<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DespesaFrete extends Model
{
    use HasFactory;

    protected $fillable = [
        'frete_id', 'tipo_despesa_id', 'fornecedor_id', 'valor', 'observacao', 'conta_pagar_id'
    ];

    public function fornecedor(){
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    public function frete(){
        return $this->belongsTo(Frete::class, 'frete_id');
    }

    public function tipoDespesaFrete(){
        return $this->belongsTo(TipoDespesaFrete::class, 'tipo_despesa_id');
    }
}
