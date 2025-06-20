<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioLocalizacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id', 'localizacao_id'
    ];

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'localizacao_id');
    }
}
