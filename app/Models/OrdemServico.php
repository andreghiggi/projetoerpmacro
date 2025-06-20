<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemServico extends Model
{
    use HasFactory;

    protected $fillable = [
        'descricao', 'cliente_id', 'usuario_id', 'empresa_id', 'valor', 'data_inicio', 'data_entrega', 'funcionario_id', 
        'forma_pagamento', 'codigo_sequencial', 'caixa_id', 'local_id', 'adiantamento'
    ];

    public function servicos(){
        return $this->hasMany(ServicoOs::class, 'ordem_servico_id', 'id');
    }

    public function itens(){
        return $this->hasMany(ProdutoOs::class, 'ordem_servico_id', 'id');
    }

    public function relatorios(){
        return $this->hasMany(RelatorioOs::class, 'ordem_servico_id', 'id');
    }

    public function funcionarios(){
        return $this->hasMany(FuncionarioOs::class, 'ordem_servico_id', 'id');
    } 

    public function funcionario(){
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function oticaOs(){
        return $this->hasOne(OticaOs::class, 'ordem_servico_id');
    }

    public function medicaoReceitaOs(){
        return $this->hasOne(MedicaoReceitaOs::class, 'ordem_servico_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

}
