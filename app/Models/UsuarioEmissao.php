<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioEmissao extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id', 'numero_serie_nfce', 'numero_ultima_nfce'
    ];

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

}
