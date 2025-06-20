<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemProducao extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'observacao', 'estado', 'data_prevista_entrega', 'funcionario_id', 'codigo_sequencial', 'usuario_id'
    ];

    public function itens()
    {
        return $this->hasMany(ItemOrdemProducao::class, 'ordem_producao_id');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public static function estados(){
        return [
            'novo' => 'Novo',
            'producao' => 'Produção',
            'expedicao' => 'Expedição',
            'entregue' => 'Entregue'
        ];
    }

}
