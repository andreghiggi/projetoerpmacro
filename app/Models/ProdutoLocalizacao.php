<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoLocalizacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'localizacao_id'
    ];

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'localizacao_id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

}
