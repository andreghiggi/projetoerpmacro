<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CTeDescarga extends Model
{
    use HasFactory;

    protected $fillable = [
        'info_id', 'chave', 'seg_cod_barras'
    ];
}
