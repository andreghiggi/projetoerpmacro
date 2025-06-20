<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrinho extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'empresa_id', 'estado', 'valor_total', 'endereco_id', 'valor_frete', 'session_cart',
        'tipo_frete', 'cep'
    ];

    public function itens(){
        return $this->hasMany(ItemCarrinho::class, 'carrinho_id');
    }

    public function endereco(){
        return $this->belongsTo(EnderecoEcommerce::class, 'endereco_id');
    }

    public function somaDimensoes(){
        $data = [
            'comprimento' => 0,
            'altura' => 0,
            'largura' => 0
        ];
        foreach($this->itens as $key => $i){
            if($i->produto->comprimento == 0 || !$i->produto->comprimento){
                $i->produto->comprimento = 13;
            }
            if($i->produto->largura == 0 || !$i->produto->largura){
                $i->produto->largura = 8;
            }
            if($i->produto->altura == 0 || !$i->produto->altura){
                $i->produto->altura = 1;
            }
            if($i->produto->comprimento > $data['comprimento']){
                $data['comprimento'] = $i->produto->comprimento;
            }

            $data['altura'] += $i->produto->altura;

            if($i->produto->largura > $data['largura']){
                $data['largura'] = $i->produto->largura;
            }

            $data['largura'] = $data['largura'];
        }
        return $data;
    }

    public function somaPeso(){
        $soma = 0;
        foreach($this->itens as $i){
            if($i->produto->peso == 0 || !$i->produto->peso){
                $i->produto->peso = 0.300;
            }
            $soma += $i->quantidade * $i->produto->peso;
        }
        return $soma;
    }
}
