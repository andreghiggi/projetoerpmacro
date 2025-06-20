<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'host', 'email', 'senha', 'porta', 'cripitografia', 'smtp_auth', 'smtp_debug',
        'nome', 'status'
    ];
}
