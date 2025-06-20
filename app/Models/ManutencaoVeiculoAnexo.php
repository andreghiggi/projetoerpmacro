<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManutencaoVeiculoAnexo extends Model
{
    use HasFactory;

    protected $fillable = [
        'manutencao_id', 'arquivo'
    ];

    public function getFileAttribute()
    {
        return "/uploads/manutencao/$this->arquivo";
    }
}
