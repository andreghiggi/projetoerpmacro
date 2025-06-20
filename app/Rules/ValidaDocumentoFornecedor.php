<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Fornecedor;

class ValidaDocumentoFornecedor implements Rule
{
    
    protected $empresa_id = null;
    protected $nome = null;
    public function __construct($empresa_id)
    {
        $this->empresa_id = $empresa_id;
    }

    public function passes($attribute, $value)
    {
        
        $fornecedor = Fornecedor::where('cpf_cnpj', $value)->where('empresa_id', $this->empresa_id)->first();
        if(empty($fornecedor)) return true;
        else{
            $this->nome = $fornecedor->razao_social;
            return false;
        }
    }

    public function message()
    {
        return "Documento já cadastrado para $this->nome";
    }

}
