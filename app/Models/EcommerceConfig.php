<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'loja_id', 'logo', 'rua', 'numero', 'bairro', 'cidade_id', 'cep', 'telefone',
        'email', 'link_facebook', 'link_whatsapp', 'link_instagram', 'frete_gratis_valor', 
        'mercadopago_public_key', 'mercadopago_access_token', 'empresa_id', 
        'habilitar_retirada', 'desconto_padrao_boleto', 'desconto_padrao_pix', 'desconto_padrao_cartao', 
        'tipos_pagamento', 'status', 'notificacao_novo_pedido', 'descricao_breve', 'politica_privacidade',
        'termos_condicoes', 'dados_deposito', 'cor_principal'
    ];

    public static function tiposPagamento(){
        return [
            'cartao' => 'Cartão de credito',
            'pix' => 'Pix',
            'boleto' => 'Boleto',
            'deposito' => 'Depósito bancário',
        ];
    }

    public function sizeColumn(){
        $tiposPagamento = json_decode($this->tipos_pagamento);
        if(sizeof($tiposPagamento) == 1){
            return "col-md-12";
        }else if(sizeof($tiposPagamento) == 2){
            return "col-md-6";
        }else if(sizeof($tiposPagamento) == 3){
            return "col-md-4";
        }
        else if(sizeof($tiposPagamento) == 4){
            return "col-md-3";
        }

    }

    public function getEnderecoAttribute()
    {
       
        return "$this->rua, $this->numero - " . $this->cidade->info;
    }

    public function getLogoImgAttribute()
    {
        if($this->logo == ""){
            return "/imgs/no-image.png";
        }
        return "/uploads/logos/$this->logo";
    }

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

}
