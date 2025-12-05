<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaServico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'empresa_id', 'imagem', 'marketplace', 'hash_delivery'
    ];


    public function getImgAttribute()
    {
        if ($this->imagem == "") {
            return "/imgs/no-image.png";
        }
        return "/uploads/categoriaServico/$this->imagem";
    }

    public function servicos()
    {
        return $this->hasMany(Servico::class, 'categoria_id', 'id');
    }

    public function servicosMarketplace()
    {
        return $this->hasMany(Servico::class, 'categoria_id', 'id')->where('marketplace', 1);
    }
}
