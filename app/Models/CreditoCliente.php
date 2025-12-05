<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditoCliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'valor', 'troca_id', 'status'
    ];

    public function tributacao(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function troca(){
        return $this->belongsTo(Troca::class, 'troca_id');
    }
}
