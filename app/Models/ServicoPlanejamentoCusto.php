<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPlanejamentoCusto extends Model
{
    use HasFactory;

    protected $fillable = [
        'planejamento_id', 'servico_id', 'quantidade', 'valor_unitario', 'sub_total', 'status', 'terceiro', 'observacao'
    ];

    public function servico(){
        return $this->belongsTo(Servico::class, 'servico_id');
    }
}
