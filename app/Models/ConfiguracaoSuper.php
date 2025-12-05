<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoSuper extends Model
{
    use HasFactory;

    protected $fillable = [
        'cpf_cnpj', 'name', 'email', 'telefone', 'mercadopago_public_key',
        'mercadopago_access_token', 'sms_key', 'token_whatsapp',
        'usuario_correios', 'codigo_acesso_correios', 'cartao_postagem_correios', 'token_auth_nfse',
        'timeout_nfe', 'timeout_nfce', 'timeout_cte', 'timeout_mdfe', 'usar_resp_tecnico',
        'token_api', 'token_integra_notas', 'banco_plano', 'asaas_token', 'auto_cadastro',
        'receber_com_boleto', 'asaas_token_boleto', 'percentual_juros_padrao_boleto', 'percentual_multa_padrao_boleto',
        'dias_atraso_suspender_boleto', 'sandbox_boleto', 'usuario_alterar_plano', 'info_topo_menu', 'dias_alerta_boleto',
        'tema_padrao', 'duplicar_cpf_cnpj', 'email_aviso_novo_cadastro', 'cobrar_apos_auto_cadastro', 'landing_page'
    ];

}
