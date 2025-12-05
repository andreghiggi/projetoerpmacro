<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'descricao', 'maximo_nfes', 'maximo_nfces', 'imagem', 'visivel_clientes',
        'status', 'valor', 'intervalo_dias', 'maximo_ctes', 'maximo_cte_os', 'maximo_mdfes', 'modulos',
        'visivel_contadores', 'auto_cadastro', 'segmento_id', 'fiscal', 'valor_implantacao', 'maximo_usuarios', 
        'maximo_locais', 'contador_id', 'descricao_curta', 'dias_teste'
    ];

    public function getImgAttribute()
    {
        if($this->imagem == ""){
            return "/imgs/no-image.png";
        }
        return "/uploads/planos/$this->imagem";
    }

    public static function formasPagamento(){
        return [
            'Dinheiro' => 'Dinheiro',
            'Cartão de crédito' => 'Cartão de crédito',
            'Cartão de débito' => 'Cartão de débito',
            'PIX' => 'PIX'
        ];
    }

    public function segmento(){
        return $this->belongsTo(Segmento::class, 'segmento_id');
    }

    public function contador(){
        return $this->belongsTo(Empresa::class, 'contador_id');
    }
    
}
