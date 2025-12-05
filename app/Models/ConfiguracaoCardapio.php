<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoCardapio extends Model
{
    use HasFactory;
    protected $fillable = [
        'empresa_id', 'nome_restaurante', 'descricao_restaurante_pt', 'descricao_restaurante_en', 
        'descricao_restaurante_es', 'logo', 'fav_icon', 'telefone', 'rua', 'numero', 'bairro',
        'cidade_id', 'api_token', 'link_instagran', 'link_facebook', 'link_whatsapp', 'intercionalizar',
        'valor_pizza', 'incluir_servico', 'percentual_taxa_servico', 'qr_code_mesa', 'cor_principal',
        'limite_pessoas_qr_code', 'confirma_mesa'
    ];

    protected $appends = [ 'logoApp' ];

    protected $hidden = [
        'api_token'
    ];

    public function getLogoAppAttribute()
    {
        if($this->logo == ""){
            return env("APP_URL") . "/imgs/no-image.png";
        }
        return env("APP_URL") . "/uploads/logos/$this->logo";
    }

    public function getLogoImgAttribute()
    {
        if($this->logo == ""){
            return "/imgs/no-image.png";
        }
        return "/uploads/logos/$this->logo";
    }

    public function getFavImgAttribute()
    {
        if($this->fav_icon == ""){
            return "/imgs/no-image.png";
        }
        return "/uploads/fav_icons/$this->fav_icon";
    }

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

}
