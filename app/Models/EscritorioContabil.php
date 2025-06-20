<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscritorioContabil extends Model
{
    use HasFactory;

    protected $fillable = [
        'razao_social', 'nome_fantasia', 'cnpj', 'ie', 'rua', 'numero', 'bairro', 'telefone', 'email', 
        'cep', 'empresa_id', 'envio_xml_automatico', 'cidade_id', 'crc', 'cpf', 'token_sieg'
    ];

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }
}
