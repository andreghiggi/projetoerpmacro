<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChaveNfeCte extends Model
{
    use HasFactory;

    protected $fillable = [
        'cte_id', 'chave'
    ];
    
}
