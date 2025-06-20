<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Cliente;

class ValidaDocumentoCliente implements Rule
{
    
    protected $empresa_id = null;
    protected $nome = null;
    public function __construct($empresa_id)
    {
        $this->empresa_id = $empresa_id;
    }

    public function passes($attribute, $value)
    {
        
        $cliente = Cliente::where('cpf_cnpj', $value)->where('empresa_id', $this->empresa_id)->first();

        if(empty($cliente)) return true;
        else{
            $this->nome = $cliente->razao_social;
            return false;
        }
    }

    public function message()
    {
        return "Documento já cadastrado para $this->nome";
    }

}
