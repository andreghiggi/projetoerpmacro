<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boleto extends Model
{
    use HasFactory;

    protected $fillable = [
        'conta_boleto_id', 'conta_receber_id', 'numero', 'numero_documento', 'carteira', 'convenio', 
        'linha_digitavel', 'nome_arquivo', 'juros', 'multa', 'juros_apos', 'instrucoes', 
        'usar_logo', 'tipo', 'codigo_cliente', 'posto', 'empresa_id', 'vencimento', 'valor', 'instrucoes'
    ];

    public function contaReceber(){
        return $this->belongsTo(ContaReceber::class, 'conta_receber_id');
    }

    public function contaBoleto(){
        return $this->belongsTo(ContaBoleto::class, 'conta_boleto_id');
    }

    public function remessa(){
        return $this->hasOne(RemessaBoletoItem::class, 'boleto_id');
    }
}
