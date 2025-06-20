<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegmentoEmpresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'segmento_id', 'empresa_id'
    ];

}
