<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoConta extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'plano_conta_id', 'descricao'
    ];

    public function grauItem(){
        $str = explode("-", $this->descricao);
        $len = strlen(trim($str[0]));
        return $len;
    }

    public function dependentes(){
        return $this->hasMany(PlanoConta::class, 'plano_conta_id');
    }
}
