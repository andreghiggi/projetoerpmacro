<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmAnotacaoNota extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_anotacao_id', 'nota'
    ];
}
