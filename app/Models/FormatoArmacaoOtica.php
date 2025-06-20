<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormatoArmacaoOtica extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nome', 'status', 'imagem'
    ];

    public function getImgAttribute()
    {
        if($this->imagem == ""){
            return env("APP_URL") . "/imgs/no-image.png";
        }
        return env("APP_URL") . "/uploads/formato_armacao/$this->imagem";
    }
}
