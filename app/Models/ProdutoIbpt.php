<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoIbpt extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'codigo', 'uf', 'descricao', 'nacional', 'estadual', 'importado',
        'municipal', 'vigencia_inicio', 'vigencia_fim', 'chave', 'versao', 'fonte'
    ];
}
