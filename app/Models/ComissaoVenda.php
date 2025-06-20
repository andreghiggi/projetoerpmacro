<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComissaoVenda extends Model
{
    use HasFactory;

    protected $fillable = [
		'funcionario_id', 'nfe_id', 'nfce_id', 'tabela', 'valor', 'status', 'empresa_id', 'valor_venda'
	];

    public function funcionario(){
		return $this->belongsTo(Funcionario::class, 'funcionario_id');
	}

	public function nfe()
    {
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }

	public function nfce()
    {
        return $this->belongsTo(Nfce::class, 'nfce_id');
    }

}
