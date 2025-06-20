<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'empresa_id'
    ];

    public function produtos(){
        return $this->hasMany(Marca::class, 'marca_id');
    }
    
}
