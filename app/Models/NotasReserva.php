<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotasReserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'reserva_id', 'texto'
    ];
}
