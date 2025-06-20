<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoIngrediente extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'ingrediente'
    ];
}
