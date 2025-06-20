<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTroca extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'quantidade', 'troca_id'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

}
