<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'unidade_cobranca', 'valor', 'categoria_id', 'empresa_id', 'tempo_servico',
        'tempo_adicional', 'valor_adicional', 'tempo_tolerancia', 'comissao', 'codigo_servico', 'aliquota_iss',
        'aliquota_pis', 'aliquota_cofins', 'aliquota_inss', 'imagem', 'status', 'codigo_tributacao_municipio',
        'reserva', 'padrao_reserva_nfse', 'marketplace', 'hash_delivery', 'descricao', 'destaque_marketplace',
        'numero_sequencial', 'aliquota_ir', 'aliquota_csll', 'valor_deducoes', 'desconto_incondicional', 'desconto_condicional',
        'outras_retencoes', 'estado_local_prestacao_servico', 'natureza_operacao', 'codigo_cnae', 'prazo_garantia'
    ];

    protected $appends = [ 'imgApp' ];

    public function getImgAppAttribute()
    {
        if($this->imagem == ""){
            return env("APP_URL") . "/imgs/no-image.png";
        }
        return env("APP_URL") . "/uploads/servicos/$this->imagem";
    }

    public function getImgAttribute()
    {
        if($this->imagem == ""){
            return "/imgs/no-image.png";
        }
        return "/uploads/servicos/$this->imagem";
    }

    public function categoria(){
        return $this->belongsTo(CategoriaServico::class, 'categoria_id');
    }
}
