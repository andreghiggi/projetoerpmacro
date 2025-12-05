<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaPagar extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nfe_id', 'fornecedor_id', 'descricao', 'valor_integral', 'valor_pago', 'data_vencimento', 
        'data_pagamento', 'status', 'observacao', 'tipo_pagamento', 'caixa_id', 'local_id', 'arquivo', 'motivo_estorno',
        'categoria_conta_id', 'observacao2', 'observacao3', 'referencia', 'conta_empresa_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaConta::class, 'categoria_conta_id');
    }

    public function fornecedor(){
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    public function contaEmpresa()
    {
        return $this->belongsTo(ContaEmpresa::class, 'conta_empresa_id');
    }

    public function nfe()
    {
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }

    public function diasAtraso(){
        $d = date('Y-m-d');
        $d2 = $this->data_vencimento;
        $dif = strtotime($d2) - strtotime($d);
        $dias = floor($dif / (60 * 60 * 24));

        if($dias == 0){
            return "conta vence hoje";
        }

        if($dias > 0){
            return "$dias dia(s) para o vencimento";
        }else{
            return "conta vencida à " . ($dias*-1) . " dia(s)";
        }
    } 

}
