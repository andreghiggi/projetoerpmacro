<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'usuario_id', 'valor_abertura', 'data_fechamento', 'observacao', 'status', 'valor_fechamento', 'valor_dinheiro',
        'valor_cheque', 'valor_outros', 'conta_empresa_id', 'local_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function contaEmpresa()
    {
        return $this->belongsTo(ContaEmpresa::class, 'conta_empresa_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }
}
