<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaBoleto extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'banco', 'agencia', 'conta', 'titular', 'padrao', 'usar_logo', 'documento', 'rua',
        'numero', 'cep', 'bairro', 'cidade_id', 'carteira', 'convenio', 'juros', 'multa', 'juros_apos',
        'tipo'
    ];

    protected $appends = [ 'info' ];

    public function getInfoAttribute()
    {
        return "$this->banco ($this->agencia - $this->conta)";
    }

    public static function bancos(){
        return [
            'Banco do brasil' => 'Banco do brasil',
            'Sicoob' => 'Sicoob',
            'Banco do nordeste' => 'Banco do nordeste',
            'Bradesco' => 'Bradesco',
            'Banco btg' => 'Banco btg',
            'C6' => 'C6',
            'Banco inter' => 'Banco inter',
            'Itau' => 'Itau',
            'Santander' => 'Santander',
            'Sicredi' => 'Sicredi',
        ];
    }

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }
}
