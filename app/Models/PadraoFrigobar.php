<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PadraoFrigobar extends Model
{
    use HasFactory;

    protected $fillable = [
        'frigobar_id', 'produto_id', 'quantidade'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
