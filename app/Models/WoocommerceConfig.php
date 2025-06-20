<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WoocommerceConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'consumer_key', 'consumer_secret', 'url'
    ];
}
