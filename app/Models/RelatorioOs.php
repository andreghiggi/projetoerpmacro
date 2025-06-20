<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatorioOs extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id', 'texto', 'ordem_servico_id'
    ];

    public function ordemServico(){
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
