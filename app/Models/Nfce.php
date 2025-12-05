<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nfce extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'emissor_nome', 'emissor_cpf_cnpj', 'cliente_nome', 'cliente_cpf_cnpj',
        'chave', 'numero_serie', 'numero', 'estado', 'total', 'motivo_rejeicao', 'recibo',
        'ambiente', 'desconto', 'acrescimo', 'natureza_id', 'observacao', 'cliente_id',
        'api', 'caixa_id', 'dinheiro_recebido', 'troco', 'tipo_pagamento', 'bandeira_cartao',
        'cnpj_cartao', 'cAut_cartao', 'gerar_conta_receber', 'valor_cashback', 'lista_id',
        'numero_sequencial', 'funcionario_id', 'local_id', 'user_id', 'valor_entrega', 'placa', 'uf', 'tipo',
        'qtd_volumes', 'numeracao_volumes', 'especie', 'peso_liquido', 'peso_bruto', 'valor_frete',
        'transportadora_id'
    ];

    public function transportadora()
    {
        return $this->belongsTo(Transportadora::class, 'transportadora_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function caixa()
    {
        return $this->belongsTo(Caixa::class, 'caixa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function listaPreco()
    {
        return $this->belongsTo(ListaPreco::class, 'lista_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    public function pedido()
    {
        return $this->hasOne(Pedido::class, 'nfce_id');
    }

    public function registroTef()
    {
        return $this->hasOne(RegistroTef::class, 'nfce_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function natureza()
    {
        return $this->belongsTo(NaturezaOperacao::class, 'natureza_id');
    }

    public function itens()
    {
        return $this->hasMany(ItemNfce::class, 'nfce_id')->with('produto');
    }

    public function troca()
    {
        return $this->hasMany(Troca::class, 'nfce_id');
    }

    public function itensServico()
    {
        return $this->hasMany(ItemServicoNfce::class, 'nfce_id')->with('servico');
    }

    public function fatura()
    {
        return $this->hasMany(FaturaNfce::class, 'nfce_id');
    }

    public function contaReceber()
    {
        return $this->hasMany(ContaReceber::class, 'nfce_id');
    }

    public function vendedor()
    {
        $funcionario = Funcionario::find($this->funcionario_id);
        if ($funcionario != null) return $funcionario->nome;
        else return '--';
    }

    public static function lastNumero($empresa)
    {
        if ($empresa->ambiente == 2) {
            return $empresa->numero_ultima_nfce_homologacao + 1;
        } else {
            return $empresa->numero_ultima_nfce_producao + 1;
        }
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
            // '99' => 'Outros',

            '30' => 'Cartão de Crédito TEF',
            '31' => 'Cartão de Débito TEF',
            '32' => 'PIX TEF',
        ];
    }

    public static function tiposPagamentoMobo()
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
            '17' => 'Pix',
        ];
    }

    public static function bandeiras()
    {
        return [
            '01' => 'Visa',
            '02' => 'Mastercard',
            '03' => 'American Express',
            '04' => 'Sorocred',
            '05' => 'Diners Club',
            '06' => 'Elo',
            '07' => 'Hipercard',
            '08' => 'Aura',
            '09' => 'Cabal',
            // '99' => 'Outros'
        ];
    }

    public static function getTipoPagamento($tipo)
    {
        if($tipo == '00'){
            return 'Vale crédito';
        }
        if (isset(Nfce::tiposPagamento()[$tipo])) {
            return Nfce::tiposPagamento()[$tipo];
        } else {
            return "Não identificado";
        }
    }

    public static function getTipoPagamentoNfce($tipo)
    {
        $values = [
            'Dinheiro' => '01',
            'Cheque' => '02',
            'Cartão de Crédito' => '03',
            'Cartão de Débito' => '04',
            'Crédito Loja' => '05',
            'Crediário' => '06',
            'Vale Alimentação' => '10',
            'Vale Refeição' => '11',
            'Vale Presente' => '12',
            'Vale Combustível' => '13',
            'Duplicata Mercantil' => '14',
            'Boleto Bancário' => '15',
            'Depósito Bancário' => '16',
            'Pagamento Instantâneo (PIX)' => '17',
            'Sem Pagamento' => '90',
            'Outros' => '99',
        ];
        try {
            return $values[$tipo];
        } catch (\Exception $e) {
            return $values["Dinheiro"];
        }
    }
}
