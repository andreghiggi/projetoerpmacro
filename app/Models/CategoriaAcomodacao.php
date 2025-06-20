<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaAcomodacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'empresa_id'
    ];
}
