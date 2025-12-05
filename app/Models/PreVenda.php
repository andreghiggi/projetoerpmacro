<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreVenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'usuario_id', 'valor_total', 'natureza_id', 'tipo_pagamento', 'forma_pagamento', 'funcionario_id', 'observacao',
        'desconto', 'acrescimo', 'empresa_id', 'bandeira_cartao', 'cnpj_cartao', 'cAut_cartao', 'descricao_pag_outros', 'rascunho', 'status', 
        'codigo', 'venda_id', 'local_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function itens()
    {
        return $this->hasMany(ItemPreVenda::class, 'pre_venda_id', 'id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    // public function vendedor()
    // {
    //     return $this->belongsTo(Funcionario::class, 'funcionario_id');
    // }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }
    
    public function vendedor()
    {
        $funcionario = Funcionario::find($this->funcionario_id);
        if ($funcionario != null) return $funcionario->nome;
        else return '--';
    }

    public function fatura()
    {
        return $this->hasMany(FaturaPreVenda::class, 'pre_venda_id', 'id');
    }

    public function nfce()
    {
        return $this->belongsTo(Nfce::class, 'venda_id');
    }

    public static function tiposPagamento()
    {
        return [
            '01' => 'Dinheiro',
            '02' => 'Cheque',
            '03' => 'Cartão de Crédito',
            '04' => 'Cartão de Débito',
            '05' => 'Crédito Loja',
            '06' => 'Crediário',
            '10' => 'Vale Alimentação',
            '11' => 'Vale Refeição',
            '12' => 'Vale Presente',
            '13' => 'Vale Combustível',
            '14' => 'Duplicata Mercantil',
            '15' => 'Boleto Bancário',
            '16' => 'Depósito Bancário',
            '17' => 'Pagamento Instantâneo (PIX)',
            '90' => 'Sem Pagamento',
            '99' => 'Outros',
        ];
    }

    
}
