<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadeCarga extends Model
{
    use HasFactory;

    protected $fillable = [
		'info_id', 'id_unidade_carga', 'quantidade_rateio'
	];
}
