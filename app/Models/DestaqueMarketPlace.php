<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestaqueMarketPlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'produto_id', 'descricao', 'valor', 'status', 'imagem', 'servico_id'
    ];

    protected $appends = [ 'imgApp' ];

    public function getImgAttribute()
    {
        if($this->imagem == ""){
            return "/imgs/no-image.png";
        }
        return "/uploads/carrossel/$this->imagem";
    }

    public function getImgAppAttribute()
    {
        if($this->imagem == ""){
            return env("APP_URL") . "/imgs/no-image.png";
        }
        return env("APP_URL") . "/uploads/carrossel/$this->imagem";
    }

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function servico(){
        return $this->belongsTo(Servico::class, 'servico_id');
    }
}
