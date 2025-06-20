<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acomodacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'empresa_id', 'numero', 'categoria_id', 'valor_diaria', 'capacidade', 'estacionamento',
        'descricao', 'status'
    ];

    public function getInfoAttribute()
    {
        return "$this->nome #$this->numero";
    }

    public function categoria(){
        return $this->belongsTo(CategoriaAcomodacao::class, 'categoria_id');
    }

    public function frigobar(){
        return $this->hasOne(Frigobar::class, 'acomodacao_id');
    }
}
