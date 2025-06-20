<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaleriaProduto extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'imagem'
    ];

    public function getImgAttribute()
    {
        if($this->imagem == ""){
            return "/imgs/no-image.png";
        }
        return "/uploads/produtos/$this->imagem";
    }
}
