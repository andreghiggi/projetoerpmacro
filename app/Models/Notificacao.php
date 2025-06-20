<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'tabela', 'descricao', 'referencia', 'status', 'por_sistema', 'prioridade', 'visualizada',
        'titulo', 'descricao_curta', 'super'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
