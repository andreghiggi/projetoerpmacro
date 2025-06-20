<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoPendente extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'valor', 'plano_id', 'contador_id'
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function contador(){
        return $this->belongsTo(Empresa::class, 'contador_id');
    }

    public function plano(){
        return $this->belongsTo(Plano::class, 'plano_id');
    }
}
