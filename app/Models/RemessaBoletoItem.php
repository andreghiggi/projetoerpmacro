<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RemessaBoletoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'remessa_id', 'boleto_id'
    ];

}
