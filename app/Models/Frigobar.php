<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frigobar extends Model
{
    use HasFactory;

    protected $fillable = [
        'acomodacao_id', 'empresa_id', 'modelo'
    ];

    public function acomodacao(){
        return $this->belongsTo(Acomodacao::class, 'acomodacao_id');
    }

    public function padraoProdutos(){
        return $this->hasMany(PadraoFrigobar::class, 'frigobar_id');
    }

}
