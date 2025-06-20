<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreteAnexo extends Model
{
    use HasFactory;

    protected $fillable = [
        'frete_id', 'arquivo'
    ];

    public function getFileAttribute()
    {
        return "/uploads/fretes/$this->arquivo";
    }
}
