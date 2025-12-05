<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtiquetaConfiguracao extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'margem_topo', 'margem_lateral', 'distancia_entre_etiquetas', 'distancia_entre_linhas',
        'largura_imagem', 'altura_imagem'    
    ];
}
