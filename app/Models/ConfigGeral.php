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
        'ultimo_codigo_fornecedor', 'app_valor_aprazo', 'impressao_sem_janela_cupom', 'alterar_valor_pdv_off',
        'resp_tec_email', 'resp_tec_cpf_cnpj', 'resp_tec_nome', 'resp_tec_telefone', 'limitar_credito_cliente',
        'documento_pdv', 'numero_inicial_comanda', 'numero_final_comanda', 'corrigir_numeracao_fiscal', 'gerar_conta_pagar_padrao',
        'home_componentes', 'token_whatsapp', 'small_header_user', 'mensagem_wpp_link', 'status_wpp_link', 'enviar_danfe_wpp_link', 
        'enviar_xml_wpp_link', 'enviar_pedido_a4_wpp_link', 'produtos_exibe_tabela', 'itens_por_pagina', 'clientes_exibe_tabela',
        'tipo_ordem_servico'
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

    public static function componentesHome(){
        return [
            'Cards de receita', 'Cards por período', 'Cards de recebimento', 'Gráfico de vendas', 'Gráfico de compras', 
            'Gráfico de emissão mensal',
            'Gráfico de emissão quantidade', 'Gráfico de emissão acumulado', 'Gráfico contas a receber', 'Gráfico contas a pagar',
            'Gráfico de emissão de CTe', 'Gráfico de emissão de MDFe'
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
