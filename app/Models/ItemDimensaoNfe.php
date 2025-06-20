<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemDimensaoNfe extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_nfe_id', 'valor_unitario_m2', 'largura', 'altura', 'quantidade', 'm2_total', 'espessura',
        'observacao', 'sub_total'
    ];
}
