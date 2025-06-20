<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'usuario_id', 'nome', 'cpf_cnpj', 'telefone', 'cidade_id', 'rua', 'numero', 'bairro', 'comissao',
        'salario', 'codigo', 'status', 'permite_alterar_valor_app'
    ];

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function funcionamento()
    {
        return $this->hasMany(Funcionamento::class, 'funcionario_id');
    }

    public function interrupcoes()
    {
        return $this->hasMany(Interrupcoes::class, 'funcionario_id');
    }

    public function eventos()
    {
        return $this->hasMany(FuncionarioEvento::class, 'funcionario_id');
    }

    public function servicos()
    {
        return $this->hasMany(FuncionarioServico::class, 'funcionario_id');
    }

    public function eventosAtivos()
    {
        return $this->hasMany(FuncionarioEvento::class, 'funcionario_id')->where('ativo', 1);
    }
}
