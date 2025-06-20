<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuncionarioServico extends Model
{
  use HasFactory;

  protected $fillable = [
    'empresa_id', 'funcionario_id', 'servico_id'
  ];


  public function servico()
  {
    return $this->belongsTo(Servico::class, 'servico_id');
  }
}
