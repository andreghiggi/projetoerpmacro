<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketPlaceConfig extends Model
{
    use HasFactory;
    protected $fillable = [
        'empresa_id', 'cidade_id', 'nome', 'descricao', 'logo', 'fav_icon', 'telefone', 'rua', 'numero', 'bairro',
        'cidade_id', 'api_token', 'link_instagram', 'link_facebook', 'link_whatsapp', 'cep', 'segmento', 
        'tempo_medio_entrega',
        'valor_entrega', 'latitude', 'longitude', 'valor_entrega_gratis', 'usar_bairros', 'status', 
        'notificacao_novo_pedido', 'mercadopago_public_key', 'mercadopago_access_token', 'tipo_divisao_pizza', 'logo',
        'fav_icon', 'tipos_pagamento', 'pedido_minimo', 'avaliacao_media', 'autenticacao_sms', 
        'confirmacao_pedido_cliente', 'tipo_entrega', 'loja_id', 'cor_principal', 'email', 'funcionamento_descricao', 
        'token_whatsapp'
    ];

    protected $appends = [ 'logoApp' ];
    
    protected $hidden = [
        'api_token'
    ];

    public static function getSegmentoServico($config){
        $segmentos = json_decode($config->segmento);
        if(in_array('servicos', $segmentos)){
            return 1;
        }
        return 0;
    }

    public function getEnderecoAttribute()
    {

        return "$this->rua, $this->numero - " . $this->cidade->info;
    }
    
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

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

    public static function tiposPagamento(){
        return [
            'Dinheiro',
            'Visa crédito',
            'Mastercard crédito',
            'Hipercard crédito',
            'Elo crédito',
            'Visa débito',
            'Mastercard débito',
            'Hipercard débito',
            'Elo débito',
            'Pix',
            'Pix pelo App',
            // 'Cartão pelo App',
        ];
    }

    public function aceitaCartao(){
        $cartoes = [
            'Visa crédito',
            'Mastercard crédito',
            'Hipercard crédito',
            'Elo crédito',
            'Visa débito',
            'Mastercard débito',
            'Hipercard débito',
            'Elo débito'
        ];
        $tipos_pagamento = json_decode($this->tipos_pagamento);
        foreach($cartoes as $c){
            if(in_array($c, $tipos_pagamento)){
                return 1;
            }
        }
        return 0;
    }

    public function tiposEntrega(){
        
        $tipo_entrega = json_decode($this->tipo_entrega);
        if(sizeof($tipo_entrega) == 2) return 'delivery-retirada';
        return $tipo_entrega[0];
    }

    public static function validaCartaoEntrega($tipos_pagamento){
        $cartoes = [
            'Visa crédito',
            'Mastercard crédito',
            'Hipercard crédito',
            'Elo crédito',
            'Visa débito',
            'Mastercard débito',
            'Hipercard débito',
            'Elo débito'
        ];
        foreach($cartoes as $c){
            if(in_array($c, $tipos_pagamento)){
                return 1;
            }
        }
    }
}
