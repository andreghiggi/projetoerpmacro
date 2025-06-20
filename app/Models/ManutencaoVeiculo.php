<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManutencaoVeiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'veiculo_id', 'fornecedor_id', 'estado', 'observacao', 'total',
        'desconto', 'acrescimo', 'data_inicio', 'data_fim', 'numero_sequencial', 'conta_pagar_id'
    ];

    public function veiculo(){
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function fornecedor(){
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    public function servicos(){
        return $this->hasMany(ManutencaoVeiculoServico::class, 'manutencao_id');
    }

    public function produtos(){
        return $this->hasMany(ManutencaoVeiculoProduto::class, 'manutencao_id');
    }

    public function anexos(){
        return $this->hasMany(ManutencaoVeiculoAnexo::class, 'manutencao_id');
    }

}
