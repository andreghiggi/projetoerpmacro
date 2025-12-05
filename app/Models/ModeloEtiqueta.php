<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeloEtiqueta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'observacao', 'empresa_id', 'altura', 'largura', 'etiquestas_por_linha',
        'distancia_etiquetas_lateral', 'distancia_etiquetas_topo', 'quantidade_etiquetas', 
        'tamanho_fonte', 'tamanho_codigo_barras', 'nome_empresa', 'nome_produto', 
        'valor_produto', 'codigo_produto', 'codigo_barras_numerico', 'tipo', 'importado_super', 'distancia_entre_linhas',
        'referencia', 'valor_atacado'
    ];
}
