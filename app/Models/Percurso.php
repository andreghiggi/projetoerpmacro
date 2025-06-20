<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Percurso extends Model
{
    use HasFactory;

    protected $fillable = [
		'mdfe_id', 'uf'
	];
}
