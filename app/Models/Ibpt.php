<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ibpt extends Model
{
    use HasFactory;

    protected $fillable = [
        'uf', 'versao'
    ];

    public function itens(){
        return $this->hasMany(ItemIbpt::class, 'ibpt_id');
    }

    public static function getItemIbpt($uf, $codigo){
        $item = ItemIbpt::
        join('ibpts', 'ibpts.id' , '=', 'item_ibpts.ibpt_id')
        ->where('ibpts.uf', $uf)
        ->where('item_ibpts.codigo', $codigo)
        ->first();

        return $item;
    }

}
