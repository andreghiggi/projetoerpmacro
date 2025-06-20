<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MunicipioCarregamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'cidade_id', 'mdfe_id'
    ];

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }
}
