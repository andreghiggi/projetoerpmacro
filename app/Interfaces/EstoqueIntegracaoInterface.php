<?php

namespace App\Interfaces;

use App\Models\Produto;
interface EstoqueIntegracaoInterface
{
    public function atualizarEstoque(Produto $produto);
}
