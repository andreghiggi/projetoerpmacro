<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RemessaBoleto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome_arquivo', 'empresa_id', 'conta_boleto_id'
    ];

    public function contaBoleto(){
        return $this->belongsTo(ContaBoleto::class, 'conta_boleto_id');
    }

    public function itens(){
        return $this->hasMany(RemessaBoletoItem::class, 'remessa_id');
    }

}
