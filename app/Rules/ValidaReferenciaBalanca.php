<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Produto;

class ValidaReferenciaBalanca implements Rule
{
    
    protected $empresa_id = null;
    protected $nome = null;
    public function __construct($empresa_id)
    {
        $this->empresa_id = $empresa_id;
    }

    public function passes($attribute, $value)
    {
        
        $produto = Produto::where('referencia_balanca', $value)->where('empresa_id', $this->empresa_id)->first();

        if(empty($produto) || $value == '' || $value == null){ 
            return true;
        }
        else{
            $this->nome = $produto->nome;
            return false;
        }
    }

    public function message()
    {
        return "Referência de balança já cadastrada para $this->nome";
    }

}
