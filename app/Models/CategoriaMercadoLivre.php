<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaMercadoLivre extends Model
{
    use HasFactory;

    protected $fillable = [
        '_id', 'nome'
    ];
}
