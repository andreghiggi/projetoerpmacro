<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LacreUnidadeCarga extends Model
{
    use HasFactory;

    protected $fillable = [
		'info_id', 'numero'
	];
}
