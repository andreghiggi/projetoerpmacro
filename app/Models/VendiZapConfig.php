<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendiZapConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'auth_id', 'auth_secret'
    ];
}
