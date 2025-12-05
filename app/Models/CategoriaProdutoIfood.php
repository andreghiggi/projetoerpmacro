<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaProdutoIfood extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'ifood_id', 'nome', 'status', 'template'
    ];
}
