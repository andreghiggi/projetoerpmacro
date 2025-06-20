<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigGeral extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'empresa_id', 'balanca_digito_verificador', 'balanca_valor_peso', 'confirmar_itens_prevenda', 'notificacoes',
        'margem_combo', 'gerenciar_estoque', 'percentual_lucro_produto', 'tipos_pagamento_pdv', 'senha_manipula_valor',
        'abrir_modal_cartao', 'percentual_desconto_orcamento', 'agrupar_itens', 'tipo_comissao', 'modelo', 'alerta_sonoro',
        'cabecalho_pdv', 'regime_nfse', 'mercadopago_public_key_pix', 'mercadopago_access_token_pix',
        'definir_vendedor_pdv_off', 'acessos_pdv_off', 'tipo_menu', 'cor_menu', 'cor_top_bar', 'usar_ibpt',
        'casas_decimais_quantidade', 'definir_vendedor_pdv', 'cliente_padrao_pdv_off', 'gerar_conta_receber_padrao',
        'mensagem_padrao_impressao_venda', 'mensagem_padrao_impressao_os', 'ultimo_codigo_produto', 'ultimo_codigo_cliente',
        'ultimo_codigo_fornecedor', 'app_valor_aprazo', 'impressao_sem_janela_cupom'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_padrao_pdv_off');
    }

    public static function getNotificacoes(){
        return [
            'Contas a pagar', 'Contas a receber', 'Alerta de estoque', 'Alerta de validade', 'Ticket'
        ];
    }

    public static function acessosPdvOff(){
        return [
            'Home', 'Cadastros'
        ];
    }

    public static function tributacoesNfse(){
        return [
            2 => 'Estimativa', 
            3 => 'Sociedade de profissionais', 
            4 => 'Cooperativa', 
            5 => 'Microempresário individual (MEI)', 
            6 => 'Microempresário e empresa de pequeno porte (MEEPP)', 
        ];
    }
    
}
