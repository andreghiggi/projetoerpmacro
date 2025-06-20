<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDespesaFrete extends Model
{
    use HasFactory;

    protected $fillable = [ 'empresa_id', 'nome', 'status' ];
}
