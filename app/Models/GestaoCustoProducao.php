<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestaoCustoProducao extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'produto_id', 'cliente_id', 'data_prevista_finalizacao', 'data_finalizacao', 'status',
        'total_custo_produtos', 'total_custo_servicos', 'desconto', 'total_final', 'frete', 'quantidade', 'usuario_id', 'numero_sequencial',
        'total_custo_outros'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function produtos()
    {
        return $this->hasMany(GestaoCustoProducaoProduto::class, 'gestao_custo_id');
    }

    public function servicos()
    {
        return $this->hasMany(GestaoCustoProducaoServico::class, 'gestao_custo_id');
    }

    public function outros()
    {
        return $this->hasMany(GestaoCustoProducaoOutroCusto::class, 'gestao_custo_id');
    }

}
