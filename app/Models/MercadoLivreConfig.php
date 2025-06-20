<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MercadoLivreConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'client_id', 'client_secret', 'access_token', 'user_id', 'code', 'url',
        'refresh_token', 'token_expira'
    ];
}
