<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sped extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_refrencia', 'empresa_id', 'saldo_credor'
    ];

    public static function motivosInventario(){
        return [
            '' => 'Selecione',
            '01' =>'No final no período',
            '02' =>'Na mudança de forma de tributação da mercadoria (ICMS)',
            '03' =>'Na solicitação da baixa cadastral, paralisação temporária e outras situações',
            '04' =>'Na alteração de regime de pagamento – condição do contribuinte',
            '05' =>'Por determinação dos fiscos.',
        ];
    }
}
