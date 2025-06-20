<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmAnotacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'cliente_id', 'fornecedor_id', 'funcionario_id', 'registro_id', 'tipo_registro', 'status',
        'conclusao', 'assunto', 'alerta', 'data_retorno', 'data_entrega'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function fornecedor(){
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    public function funcionario(){
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function notas(){
        return $this->hasMany(CrmAnotacaoNota::class, 'crm_anotacao_id');
    }

    public function registro(){
        return $this->belongsTo(Nfe::class, 'registro_id');
    }

    public static function getStatus(){
        return [
            'positivo' => 'Positivo',
            'bom' => 'Bom',
            'negativo' => 'Negativo'
        ];
    }

    public static function getConclusoes(){
        return [
            'Venda concluida' => 'Venda concluida',
            'Venda perdida' => 'Venda perdida'
        ];
    }

}
