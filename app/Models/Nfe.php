<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nfe extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'emissor_nome', 'emissor_cpf_cnpj', 'cliente_id', 'transportadora_id',
        'chave', 'numero_serie', 'numero', 'estado', 'total', 'sequencia_cce', 'motivo_rejeicao',
        'recibo', 'ambiente', 'desconto', 'acrescimo', 'valor_produtos', 'placa', 'tipo', 'uf',
        'numeracao_volumes', 'peso_liquido', 'peso_bruto', 'especie', 'qtd_volumes', 'valor_frete',
        'natureza_id', 'observacao', 'api', 'aut_xml', 'referencia', 'tpNF', 'finNFe', 'fornecedor_id',
        'caixa_id', 'gerar_conta_receber', 'gerar_conta_pagar', 'chave_importada', 'orcamento', 'ref_orcamento',
        'data_emissao_saida', 'data_emissao_retroativa', 'bandeira_cartao', 'cnpj_cartao', 'cAut_cartao', 'tipo_pagamento',
        'numero_sequencial', 'crt', 'local_id', 'user_id', 'data_entrega', 'funcionario_id',
        'nome_entrega', 'documento_entrega', 'rua_entrega', 'numero_entrega', 'bairro_entrega', 'cep_entrega', 'complemento_entrega',
        'cidade_id_entrega', 'marca'
    ];

    public function getInfoAttribute()
    {
        $razaoSocial = "";
        if($this->cliente){
            $razaoSocial = $this->cliente->info;
        }
        if($this->fornecedor){
            $razaoSocial = $this->fornecedor->info;
        }
        return $this->numero_sequencial . " - " . $razaoSocial . " | R$" . __moeda($this->total);
    }

    public function getFinNFe()
    {
        if ($this->finNFe == 1) {
            return 'NFe normal';
        } else if ($this->finNFe == 1) {
            return 'NFe complementar';
        } else if ($this->finNFe == 1) {
            return 'NFe de ajuste';
        } else {
            return 'Devolução de mercadoria';
        }
    }

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

    public function caixa()
    {
        return $this->belongsTo(Caixa::class, 'caixa_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    public function cidadeEntrega()
    {
        return $this->belongsTo(Cidade::class, 'cidade_id_entrega');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    public function natureza()
    {
        return $this->belongsTo(NaturezaOperacao::class, 'natureza_id');
    }

    public function transportadora()
    {
        return $this->belongsTo(Transportadora::class, 'transportadora_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function manifesto()
    {
        return $this->hasOne(ManifestoDfe::class, 'compra_id');
    }

    public function pedidoMercadoLivre()
    {
        return $this->hasOne(PedidoMercadoLivre::class, 'nfe_id');
    }

    public function pedidoNuvemShop()
    {
        return $this->hasOne(NuvemShopPedido::class, 'nfe_id');
    }

    public function pedidoWoocomerce()
    {
        return $this->hasOne(WoocommercePedido::class, 'nfe_id');
    }

    public function reserva()
    {
        return $this->hasOne(Reserva::class, 'nfe_id');
    }

    public function pedidoEcommerce()
    {
        return $this->hasOne(PedidoEcommerce::class, 'nfe_id');
    }

    public function ordemServico()
    {
        return $this->hasOne(OrdemServico::class, 'nfe_id');
    }

    public function itens()
    {
        return $this->hasMany(ItemNfe::class, 'nfe_id')->with(['produtoVariacao', 'produto']);
    }

    public function produtoUnicos()
    {
        return $this->hasMany(ProdutoUnico::class, 'nfe_id');
    }

    public function fatura()
    {
        return $this->hasMany(FaturaNfe::class, 'nfe_id');
    }

    public function contasReceber()
    {
        return $this->hasMany(ContaReceber::class, 'nfe_id');
    }

    public function troca()
    {
        return $this->hasMany(Troca::class, 'nfe_id');
    }

    public static function lastNumero($empresa)
    {
        if ($empresa->ambiente == 2) {
            return $empresa->numero_ultima_nfe_homologacao + 1;
        } else {
            return $empresa->numero_ultima_nfe_producao + 1;
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
            '18' => 'Transferência bancária, Carteira Digital',
            '19' => 'Programa de fidelidade, Cashback, Crédito Virtual',
            // '20' => 'Pagamento Instantâneo (PIX) – Estático',
            // '21' => 'Crédito em Loja',
            // '22' => 'Pagamento Eletrônico não Informado - falha de hardware do sistema emissor',
            '90' => 'Sem Pagamento',
            // '99' => 'Outros',
        ];
    }

    public static function tiposFrete()
    {
        return [
            '9' => 'Sem Ocorrência de Transporte',
            '0' => 'Contratação do Frete por Conta do Remetente (CIF)',
            '1' => 'Contratação do Frete por Conta do Destinatário (FOB)',
            '2' => 'Contratação do Frete por Conta de Terceiro',
            '3' => 'Transporte Próprio por Conta do Remetente',
            '4' => 'Transporte Próprio por Conta do Destintário'
        ];
    }

    public static function getTipoPagamentoNFe($tipo)
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
            // 'Outros' => '99',
        ];
        try {
            return $values[$tipo];
        } catch (\Exception $e) {
            return $values["Dinheiro"];
        }
    }

    public static function getTipo($tipo)
    {
        if (isset(Nfe::tiposPagamento()[$tipo])) {
            return Nfe::tiposPagamento()[$tipo];
        } else {
            return "Não identificado";
        }
        // $tipos = Venda::tiposPagamento();
        // return $tipos[$tipo];
    }

    public function getTipoPagamento()
    {
        foreach (Nfe::tiposPagamento() as $key => $t) {
            if ($this->tipo_pagamento == $key) return $t;
        }
    }

    public function isItemValidade ()
    {
        foreach($this->itens as $i){
            if($i->produto->alerta_validade > 0) 
                return 1;
        }
        return 0;
    }

    public static function getEstadoImport($estado){

        if($estado == 'DISPONIVEL') return 'novo';
        else if($estado == 'REJEITADO') return 'rejeitado';
        else if($estado == 'CANCELADO') return 'cancelado';
        else if($estado == 'APROVADO') return 'aprovado';
        else return $estado;
    }

    public static function getTiposViaTransp(){
        return [
            '1' => 'Marítima',
            '2' => 'Fluvial',
            '3' => 'Lacustre',
            '4' => 'Aérea',
            '5' => 'Postal',
            '6' => 'Ferroviária',
            '7' => 'Rodoviária',
            '8' => 'Conduto/Rede transmissão',
            '9' => 'Meios próprios',
            '10' => 'Entrada/Saída ficta',
            '11' => 'Courier',
            '12' => 'Em mãos',
            '13' => 'Por reboque'
        ];
    }

    public static function getTiposIntermedio(){
        return [
            '1' => 'Importação por conta própria',
            '2' => 'Importação por conta e ordem',
            '3' => 'Importação por encomenda'
        ];
    }
}
