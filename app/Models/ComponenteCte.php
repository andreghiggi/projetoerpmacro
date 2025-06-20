<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponenteCte extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'valor', 'cte_id'
    ];
}
