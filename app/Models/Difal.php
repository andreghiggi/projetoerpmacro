<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Difal extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'uf', 'pICMSUFDest', 'pICMSInter', 'pICMSInterPart', 'cfop', 'pFCPUFDest'
    ];
}
