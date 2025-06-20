<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContadorEmpresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'contador_id'
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function __planoPendente(){
        return PlanoPendente::where('empresa_id', $this->empresa_id)
        ->where('status', 0)
        ->first();
    }
}
