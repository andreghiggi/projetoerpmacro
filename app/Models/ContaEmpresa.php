<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaEmpresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nome', 'banco', 'agencia', 'conta', 'plano_conta_id', 'saldo_inicial', 'status', 'saldo',
        'local_id'
    ];

    public function plano(){
        return $this->belongsTo(PlanoConta::class, 'plano_conta_id');
    }

    public function itens()
    {
        return $this->hasMany(ItemContaEmpresa::class, 'conta_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }
}
