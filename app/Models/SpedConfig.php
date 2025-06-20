<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpedConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'codigo_conta_analitica', 'codigo_receita', 'gerar_bloco_k', 'layout_bloco_k',
        'codigo_obrigacao', 'data_vencimento'
    ];

    public static function codigosDeObrigacao(){
        return [
            '000' => 'ICMS a recolher',
            '001' => 'ICMS da substituição tributária pelas entradas',
            '002' => 'ICMS da substituição tributária pelas saídas para o Estado',
            '003' => 'Antecipação do diferencial de alíquotas do ICMS',
            '004' => 'Antecipação do ICMS da importação',
            '005' => 'Antecipação tributária',
            '006' => 'ICMS resultante da alíquota adicional dos itens incluídos no Fundo de Combate à Pobreza',
            '090' => 'Outras obrigações do ICMS',
            '999' => 'ICMS da substituição tributária pelas saídas para outro Estado',
        ];
    }
    
}
