<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnderecoDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'cidade_id', 'cliente_id', 'bairro_id', 'rua', 'numero', 'referencia', 'latitude',
        'longitude', 'cep', 'tipo', 'padrao'
    ];

    protected $appends = [ 'info' ];

    public function getInfoAttribute()
    {
        $end = "$this->rua, $this->numero - " . $this->bairro->nome . " $this->referencia";
        if($this->tipo == 'casa'){
            $end .= ' - Casa';
        }else{
            $end .= ' - Trabalho';
        }

        return $end;
    }

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function bairro(){
        return $this->belongsTo(BairroDelivery::class, 'bairro_id');
    }
}
