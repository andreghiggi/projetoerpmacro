<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioEmpresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'usuario_id'
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
