<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'usuario_id', 'valor_abertura', 'data_fechamento', 'observacao', 'status', 'valor_fechamento', 'valor_dinheiro',
        'valor_cheque', 'valor_outros', 'conta_empresa_id', 'local_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function contaEmpresa()
    {
        return $this->belongsTo(ContaEmpresa::class, 'conta_empresa_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    public function suprimentos()
    {
        return $this->hasMany(SuprimentoCaixa::class, 'caixa_id');
    }

    public function sangrias()
    {
        return $this->hasMany(SangriaCaixa::class, 'caixa_id');
    }

    public function vendas()
    {
        return $this->hasMany(Nfe::class, 'caixa_id')->where('tpNF', 1)->where('estado', '!=', 'cancelado');
    }

    public function vendasPdv()
    {
        return $this->hasMany(Nfce::class, 'caixa_id')->where('estado', '!=', 'cancelado');
    }

}
