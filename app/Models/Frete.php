<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frete extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'veiculo_id', 'cliente_id', 'estado', 'observacao', 'total',
        'desconto', 'acrescimo', 'local_id', 'cidade_carregamento', 'cidade_descarregamento' , 'distancia_km', 'data_inicio',
        'data_fim', 'horario_inicio', 'horario_fim', 'total_despesa', 'numero_sequencial'
    ];

    public function veiculo(){
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    public function despesas()
    {
        return $this->hasMany(DespesaFrete::class, 'frete_id');
    }

    public function cidadeCarregamento()
    {
        return $this->belongsTo(Cidade::class, 'cidade_carregamento');
    }

    public function cidadeDescarregamento()
    {
        return $this->belongsTo(Cidade::class, 'cidade_descarregamento');
    }

    public function anexos(){
        return $this->hasMany(FreteAnexo::class, 'frete_id');
    }

}
