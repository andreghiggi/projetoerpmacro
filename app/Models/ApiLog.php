<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'token', 'status', 'descricao', 'tipo', 'prefixo'
    ];

    public function getPrefixo(){
        $permissoes = ApiConfig::permissoes();
        return $permissoes[$this->prefixo];
    }

    public function getTipo(){
        $acoes = ApiConfig::acoes();
        return $acoes[$this->tipo];
    }

}
