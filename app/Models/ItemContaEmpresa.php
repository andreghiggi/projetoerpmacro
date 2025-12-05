<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemContaEmpresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'conta_id', 'descricao', 'tipo_pagamento', 'valor', 'caixa_id', 'tipo', 'saldo_atual', 'cliente_id', 'fornecedor_id', 'numero_documento',
        'categoria_id', 'conta_pagar_id', 'conta_receber_id'
    ];

    public function conta(){
        return $this->belongsTo(ContaEmpresa::class, 'conta_id');
    }

    public function caixa(){
        return $this->belongsTo(Caixa::class, 'caixa_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function fornecedor(){
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    public function categoria(){
        return $this->belongsTo(CategoriaConta::class, 'categoria_id');
    }

    public function contaPagar(){
        return $this->belongsTo(ContaPagar::class, 'conta_pagar_id');
    }

    public function contaReceber(){
        return $this->belongsTo(ContaReceber::class, 'conta_receber_id');
    }
}
