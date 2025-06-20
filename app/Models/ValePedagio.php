<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValePedagio extends Model
{
    use HasFactory;

    protected $fillable = [
		'mdfe_id', 'cnpj_fornecedor', 'cnpj_fornecedor_pagador', 'numero_compra', 'valor'
	];
}
