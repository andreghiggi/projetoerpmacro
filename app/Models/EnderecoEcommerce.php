<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnderecoEcommerce extends Model
{
    use HasFactory;

    protected $fillable = [
        'cidade_id', 'cliente_id', 'rua', 'numero', 'referencia', 'cep', 'padrao', 'bairro'
    ];

    public function getInfoAttribute()
    {
        return "$this->rua, $this->numero - " . $this->bairro . " - " . $this->cidade->info;
    }

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

}
