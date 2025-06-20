<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemNotaServico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nota_servico_id', 'discriminacao', 'valor_servico', 'servico_id', 'codigo_cnae', 'codigo_tributacao_municipio', 
        'exigibilidade_iss', 'iss_retido', 'data_competencia', 'estado_local_prestacao_servico', 
        'cidade_local_prestacao_servico', 'valor_deducoes', 'desconto_incondicional', 'desconto_condicional',
        'outras_retencoes', 'aliquota_iss', 'aliquota_pis', 'aliquota_cofins', 'aliquota_inss', 
        'aliquota_ir', 'aliquota_csll', 'intermediador', 'documento_intermediador', 'nome_intermediador', 
        'im_intermediador', 'codigo_servico', 'responsavel_retencao_iss'
    ];

    public function servico(){
        return $this->belongsTo(Servico::class, 'servico_id');
    }
}
