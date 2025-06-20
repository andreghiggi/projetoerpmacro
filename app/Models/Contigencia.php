<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contigencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'status', 'tipo', 'motivo', 'status_retorno', 'documento'
    ];

    public static function tiposContigencia(){
        return [
            'SVCRS' => 'SVCRS',
            'SVCAN' => 'SVCAN',
            'OFFLINE' => 'OFFLINE',
        ];
    }
}
