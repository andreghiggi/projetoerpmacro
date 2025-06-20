<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoDescarga extends Model
{
    use HasFactory;

    protected $fillable = [
		'mdfe_id', 'tp_unid_transp', 'id_unid_transp', 'quantidade_rateio', 'cidade_id'
	];

	public function cidade(){
		return $this->belongsTo(Cidade::class, 'cidade_id');
	}

	public function cte(){
		return $this->hasOne(CTeDescarga::class, 'info_id', 'id');
	}

	public function nfe(){
		return $this->hasOne(NFeDescarga::class, 'info_id', 'id');
	}

	public function lacresTransp(){
		return $this->hasMany(LacreTransporte::class, 'info_id', 'id');
	}

	public function unidadeCarga(){
		return $this->hasOne(UnidadeCarga::class, 'info_id', 'id');
	}

	public function lacresUnidCarga(){
		return $this->hasMany(LacreUnidadeCarga::class, 'info_id', 'id');
	}
}
