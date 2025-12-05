<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoCusto extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'cliente_id', 'descricao', 'observacao', 'data_prevista_entrega', 'data_entrega', 'arquivo',
        'usuario_id', 'estado', 'compra_id', 'venda_id', 'numero_sequencial', 'local_id', 'total_custo', 'desconto', 'total_final',
        '_id', 'numero_sequencial_ano'
    ];

    public function getInfoAttribute()
    {
        return "$this->_id - " .$this->cliente->info;
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function planejamentos()
    {
        return $this->hasMany(PlanejamentoCusto::class, 'projeto_id');
    }

    public static function estados(){
        return [
            'novo' => 'Novo',
            'cotacao' => 'Cotação',
            'proposta' => 'Proposta',
            'producao' => 'Produção',
            'cancelado' => 'Cancelado',
            'finalizado' => 'Finalizado',
        ];
    }

    public function _estado(){
        if($this->estado == 'novo'){
            return "<h5 class='badge bg-light text-dark'>NOVO</h5>";
        }else if($this->estado == 'proposta'){
            return "<h5 class='badge bg-primary'>PROPOSTA</h5>";
        }else if($this->estado == 'cotacao'){
            return "<h5 class='badge bg-info'>COTAÇÃO</h5>";
        }else if($this->estado == 'producao'){
            return "<h5 class='badge bg-dark'>PRODUÇÃO</h5>";
        }else if($this->estado == 'cancelado'){
            return "<h5 class='badge bg-danger'>CANCELADO</h5>";
        }else{
            return "<h5 class='badge bg-success'>FINALIZADO</h5>";
        }
    }
}
