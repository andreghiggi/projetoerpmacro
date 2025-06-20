<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TefMultiPlusCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'usuario_id', 'cnpj', 'pdv', 'token', 'status'
    ];

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
