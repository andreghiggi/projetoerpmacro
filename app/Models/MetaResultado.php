<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaResultado extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'funcionario_id', 'valor', 'local_id', 'tabela'
    ];

    public static function tabelas(){
        return [
            'Vendas' => 'Vendas',
            'Ordens de Serviço' => 'Ordens de Serviço'
        ];
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }
}
