<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariacaoModelo extends Model
{
    use HasFactory;

    protected $fillable = [
        'descricao', 'status', 'empresa_id', 'vendizap_id'
    ];

    public function itens()
    {
        return $this->hasMany(VariacaoModeloItem::class, 'variacao_modelo_id');
    }

    public function valores(){
        $str = '';
        foreach($this->itens as $i){
            $str .= "$i->nome, ";
        }
        return substr($str, 0, strlen($str)-2);
    }
}
