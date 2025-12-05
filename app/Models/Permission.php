<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    public static function defaultPermissions()
    {
        return [
            array('name' => 'usuarios_view', 'description' => 'Visualiza usuários'),
            array('name' => 'usuarios_create', 'description' => 'Cria usuário'),
            array('name' => 'usuarios_edit', 'description' => 'Edita usuário'),
            array('name' => 'usuarios_delete', 'description' => 'Deleta usuário'),

            array('name' => 'produtos_view', 'description' => 'Visualiza produtos'),
            array('name' => 'produtos_create', 'description' => 'Cria produto'),
            array('name' => 'produtos_edit', 'description' => 'Edita produtos'),
            array('name' => 'produtos_delete', 'description' => 'Deleta produtos'),

            array('name' => 'estoque_view', 'description' => 'Visualiza estoque'),
            array('name' => 'estoque_create', 'description' => 'Cria estoque'),
            array('name' => 'estoque_edit', 'description' => 'Edita estoque'),
            array('name' => 'estoque_delete', 'description' => 'Deleta estoque'),

            array('name' => 'variacao_view', 'description' => 'Visualiza variação'),
            array('name' => 'variacao_create', 'description' => 'Cria variação'),
            array('name' => 'variacao_edit', 'description' => 'Edita variação'),
            array('name' => 'variacao_delete', 'description' => 'Deleta variação'),

            array('name' => 'categoria_produtos_view', 'description' => 'Visualiza categoria de produtos'),
            array('name' => 'categoria_produtos_create', 'description' => 'Cria categoria de produtos'),
            array('name' => 'categoria_produtos_edit', 'description' => 'Edita categoria de produtos'),
            array('name' => 'categoria_produtos_delete', 'description' => 'Deleta categoria de produtos'),

            array('name' => 'marcas_view', 'description' => 'Visualiza marca'),
            array('name' => 'marcas_create', 'description' => 'Cria marca'),
            array('name' => 'marcas_edit', 'description' => 'Edita marca'),
            array('name' => 'marcas_delete', 'description' => 'Deleta marca'),// aqui

            array('name' => 'lista_preco_view', 'description' => 'Visualiza lista de preços'),
            array('name' => 'lista_preco_create', 'description' => 'Cria lista de preços'),
            array('name' => 'lista_preco_edit', 'description' => 'Edita lista de preços'),
            array('name' => 'lista_preco_delete', 'description' => 'Deleta lista de preços'),

            array('name' => 'config_produto_fiscal_view', 'description' => 'Visualiza configuração fiscal produto'),
            array('name' => 'config_produto_fiscal_create', 'description' => 'Cria configuração fiscal produto'),
            array('name' => 'config_produto_fiscal_edit', 'description' => 'Edita configuração fiscal produto'),
            array('name' => 'config_produto_fiscal_delete', 'description' => 'Deleta configuração fiscal produto'),

            array('name' => 'atribuicoes_view', 'description' => 'Visualiza atribuições'),
            array('name' => 'atribuicoes_create', 'description' => 'Cria atribuição'),
            array('name' => 'atribuicoes_edit', 'description' => 'Edita atribuições'),
            array('name' => 'atribuicoes_delete', 'description' => 'Deleta atribuições'),

            array('name' => 'clientes_view', 'description' => 'Visualiza clientes'),
            array('name' => 'clientes_create', 'description' => 'Cria cliente'),
            array('name' => 'clientes_edit', 'description' => 'Edita cliente'),
            array('name' => 'clientes_delete', 'description' => 'Deleta cliente'),

            array('name' => 'fornecedores_view', 'description' => 'Visualiza fornecedores'),
            array('name' => 'fornecedores_create', 'description' => 'Cria fornecedor'),
            array('name' => 'fornecedores_edit', 'description' => 'Edita fornecedor'),
            array('name' => 'fornecedores_delete', 'description' => 'Deleta fornecedor'),

            array('name' => 'transportadoras_view', 'description' => 'Visualiza transportadora'),
            array('name' => 'transportadoras_create', 'description' => 'Cria transportadora'),
            array('name' => 'transportadoras_edit', 'description' => 'Edita transportadora'),
            array('name' => 'transportadoras_delete', 'description' => 'Deleta transportadora'),

            array('name' => 'nfe_view', 'description' => 'Visualiza NFe'),
            array('name' => 'nfe_create', 'description' => 'Cria NFe'),
            array('name' => 'nfe_edit', 'description' => 'Edita NFe'),
            array('name' => 'nfe_delete', 'description' => 'Deleta NFe'),
            array('name' => 'nfe_inutiliza', 'description' => 'Inutiliza NFe'),
            array('name' => 'nfe_transmitir', 'description' => 'Transmitir NFe'),

            array('name' => 'orcamento_view', 'description' => 'Visualiza Orçamento'),
            array('name' => 'orcamento_create', 'description' => 'Cria Orçamento'),
            array('name' => 'orcamento_edit', 'description' => 'Edita Orçamento'),
            array('name' => 'orcamento_delete', 'description' => 'Deleta Orçamento'),

            array('name' => 'nfce_view', 'description' => 'Visualiza NFCe'),
            array('name' => 'nfce_create', 'description' => 'Cria NFCe'),
            array('name' => 'nfce_edit', 'description' => 'Edita NFCe'),
            array('name' => 'nfce_delete', 'description' => 'Deleta NFCe'),
            array('name' => 'nfce_transmitir', 'description' => 'Transmitir NFCe'),
            array('name' => 'nfce_inutiliza', 'description' => 'Inutiliza NFce'),

            array('name' => 'cte_view', 'description' => 'Visualiza CTe'),
            array('name' => 'cte_create', 'description' => 'Cria CTe'),
            array('name' => 'cte_edit', 'description' => 'Edita CTe'),
            array('name' => 'cte_delete', 'description' => 'Deleta CTe'),

            array('name' => 'cte_os_view', 'description' => 'Visualiza CTeOs'),
            array('name' => 'cte_os_create', 'description' => 'Cria CTeOs'),
            array('name' => 'cte_os_edit', 'description' => 'Edita CTeOs'),
            array('name' => 'cte_os_delete', 'description' => 'Deleta CTeOs'),

            array('name' => 'mdfe_view', 'description' => 'Visualiza MDFe'),
            array('name' => 'mdfe_create', 'description' => 'Cria MDFe'),
            array('name' => 'mdfe_edit', 'description' => 'Edita MDFe'),
            array('name' => 'mdfe_delete', 'description' => 'Deleta MDFe'),

            array('name' => 'nfse_view', 'description' => 'Visualiza NFSe'),
            array('name' => 'nfse_create', 'description' => 'Cria NFSe'),
            array('name' => 'nfse_edit', 'description' => 'Edita NFSe'),
            array('name' => 'nfse_delete', 'description' => 'Deleta NFSe'),

            array('name' => 'pdv_view', 'description' => 'Visualiza PDV'),
            array('name' => 'pdv_create', 'description' => 'Cria PDV'),
            array('name' => 'pdv_edit', 'description' => 'Edita PDV'),
            array('name' => 'pdv_delete', 'description' => 'Deleta PDV'),

            array('name' => 'pre_venda_view', 'description' => 'Visualiza pré venda'),
            array('name' => 'pre_venda_create', 'description' => 'Cria pré venda'),
            array('name' => 'pre_venda_edit', 'description' => 'Edita pré venda'),
            array('name' => 'pre_venda_delete', 'description' => 'Deleta pré venda'),

            array('name' => 'agendamento_view', 'description' => 'Visualiza agendamento'),
            array('name' => 'agendamento_create', 'description' => 'Cria agendamento'),
            array('name' => 'agendamento_edit', 'description' => 'Edita agendamento'),
            array('name' => 'agendamento_delete', 'description' => 'Deleta agendamento'),

            array('name' => 'servico_view', 'description' => 'Visualiza serviço'),
            array('name' => 'servico_create', 'description' => 'Cria serviço'),
            array('name' => 'servico_edit', 'description' => 'Edita serviço'),
            array('name' => 'servico_delete', 'description' => 'Deleta serviço'),

            array('name' => 'categoria_servico_view', 'description' => 'Visualiza categoria de serviço'),
            array('name' => 'categoria_servico_create', 'description' => 'Cria categoria de serviço'),
            array('name' => 'categoria_servico_edit', 'description' => 'Edita categoria de serviço'),
            array('name' => 'categoria_servico_delete', 'description' => 'Deleta categoria de serviço'),

            array('name' => 'veiculos_view', 'description' => 'Visualiza veículo'),
            array('name' => 'veiculos_create', 'description' => 'Cria veículo'),
            array('name' => 'veiculos_edit', 'description' => 'Edita veículo'),
            array('name' => 'veiculos_delete', 'description' => 'Deleta veículo'),

            array('name' => 'atendimentos_view', 'description' => 'Visualiza atendimento'),
            array('name' => 'atendimentos_create', 'description' => 'Cria atendimento'),
            array('name' => 'atendimentos_edit', 'description' => 'Edita atendimento'),
            array('name' => 'atendimentos_delete', 'description' => 'Deleta atendimento'),

            array('name' => 'conta_pagar_view', 'description' => 'Visualiza conta a pagar'),
            array('name' => 'conta_pagar_create', 'description' => 'Cria conta a pagar'),
            array('name' => 'conta_pagar_edit', 'description' => 'Edita conta a pagar'),
            array('name' => 'conta_pagar_delete', 'description' => 'Deleta conta a pagar'),

            array('name' => 'conta_receber_view', 'description' => 'Visualiza conta a receber'),
            array('name' => 'conta_receber_create', 'description' => 'Cria conta a receber'),
            array('name' => 'conta_receber_edit', 'description' => 'Edita conta a receber'),
            array('name' => 'conta_receber_delete', 'description' => 'Deleta conta a receber'),

            array('name' => 'cardapio_view', 'description' => 'Visualiza cárdapio'),

            array('name' => 'controle_acesso_view', 'description' => 'Visualiza controle de acesso'),
            array('name' => 'controle_acesso_create', 'description' => 'Cria controle de acesso'),
            array('name' => 'controle_acesso_edit', 'description' => 'Edita controle de acesso'),
            array('name' => 'controle_acesso_delete', 'description' => 'Deleta controle de acesso'),

            array('name' => 'arquivos_xml_view', 'description' => 'Visualiza arquivos xml'),

            array('name' => 'natureza_operacao_view', 'description' => 'Visualiza natureza de operação'),
            array('name' => 'natureza_operacao_create', 'description' => 'Cria natureza de operação'),
            array('name' => 'natureza_operacao_edit', 'description' => 'Edita natureza de operação'),
            array('name' => 'natureza_operacao_delete', 'description' => 'Deleta natureza de operação'),

            array('name' => 'emitente_view', 'description' => 'Visualiza emitente'),

            array('name' => 'compras_view', 'description' => 'Visualiza compras'),
            array('name' => 'compras_create', 'description' => 'Cria compras'),
            array('name' => 'compras_edit', 'description' => 'Edita compras'),
            array('name' => 'compras_delete', 'description' => 'Deleta compras'),

            array('name' => 'manifesto_view', 'description' => 'Visualiza manifesto compras'),

            array('name' => 'cotacao_view', 'description' => 'Visualiza cotação'),
            array('name' => 'cotacao_create', 'description' => 'Cria cotação'),
            array('name' => 'cotacao_edit', 'description' => 'Edita cotação'),
            array('name' => 'cotacao_delete', 'description' => 'Deleta cotação'),

            array('name' => 'devolucao_view', 'description' => 'Visualiza devolução'),
            array('name' => 'devolucao_create', 'description' => 'Cria devolução'),
            array('name' => 'devolucao_edit', 'description' => 'Edita devolução'),
            array('name' => 'devolucao_delete', 'description' => 'Deleta devolução'),

            array('name' => 'funcionario_view', 'description' => 'Visualiza funcionário'),
            array('name' => 'funcionario_create', 'description' => 'Cria funcionário'),
            array('name' => 'funcionario_edit', 'description' => 'Edita funcionário'),
            array('name' => 'funcionario_delete', 'description' => 'Deleta funcionário'),

            array('name' => 'apuracao_mensal_view', 'description' => 'Visualiza Apuração mensal'),
            array('name' => 'apuracao_mensal_create', 'description' => 'Cria Apuração mensal'),
            array('name' => 'apuracao_mensal_edit', 'description' => 'Edita Apuração mensal'),
            array('name' => 'apuracao_mensal_delete', 'description' => 'Deleta Apuração mensal'),

            array('name' => 'ecommerce_view', 'description' => 'Visualiza ecommerce'),
            array('name' => 'delivery_view', 'description' => 'Visualiza delivery'),
            array('name' => 'mercado_livre_view', 'description' => 'Visualiza mercado livre'),
            array('name' => 'nuvem_shop_view', 'description' => 'Visualiza nuvem shop'),

            array('name' => 'relatorio_view', 'description' => 'Visualiza relatório'),
            array('name' => 'caixa_view', 'description' => 'Visualiza caixa'),

            array('name' => 'contas_empresa_view', 'description' => 'Visualiza contas da empresa'),
            array('name' => 'contas_empresa_create', 'description' => 'Cria contas da empresa'),
            array('name' => 'contas_empresa_edit', 'description' => 'Edita contas da empresa'),
            array('name' => 'contas_empresa_delete', 'description' => 'Deleta contas da empresa'),
            // aqui

            array('name' => 'contas_boleto_view', 'description' => 'Visualiza contas de boleto'),
            array('name' => 'contas_boleto_create', 'description' => 'Cria contas de boleto'),
            array('name' => 'contas_boleto_edit', 'description' => 'Edita contas de boleto'),
            array('name' => 'contas_boleto_delete', 'description' => 'Deleta contas de boleto'),

            array('name' => 'boleto_view', 'description' => 'Visualiza boleto'),
            array('name' => 'boleto_create', 'description' => 'Cria boleto'),
            array('name' => 'boleto_edit', 'description' => 'Edita boleto'),
            array('name' => 'boleto_delete', 'description' => 'Deleta boleto'),

            array('name' => 'taxa_pagamento_view', 'description' => 'Visualiza taxa de pagamento'),
            array('name' => 'taxa_pagamento_create', 'description' => 'Cria taxa de pagamento'),
            array('name' => 'taxa_pagamento_edit', 'description' => 'Edita taxa de pagamento'),
            array('name' => 'taxa_pagamento_delete', 'description' => 'Deleta taxa de pagamento'),

            array('name' => 'ordem_servico_view', 'description' => 'Visualiza ordem de serviço'),
            array('name' => 'ordem_servico_create', 'description' => 'Cria ordem de serviço'),
            array('name' => 'ordem_servico_edit', 'description' => 'Edita ordem de serviço'),
            array('name' => 'ordem_servico_delete', 'description' => 'Deleta ordem de serviço'),

            array('name' => 'ordem_producao_view', 'description' => 'Visualiza ordem de produção'),
            array('name' => 'ordem_producao_create', 'description' => 'Cria ordem de produção'),
            array('name' => 'ordem_producao_edit', 'description' => 'Edita ordem de produção'),
            array('name' => 'ordem_producao_delete', 'description' => 'Deleta ordem de produção'),

            array('name' => 'difal_view', 'description' => 'Visualiza difal'),
            array('name' => 'difal_create', 'description' => 'Cria difal'),
            array('name' => 'difal_edit', 'description' => 'Edita difal'),
            array('name' => 'difal_delete', 'description' => 'Deleta difal'),

            array('name' => 'cashback_config_view', 'description' => 'Visualiza cashback config'),

            array('name' => 'localizacao_view', 'description' => 'Visualiza localização'),
            array('name' => 'localizacao_create', 'description' => 'Cria localização'),
            array('name' => 'localizacao_edit', 'description' => 'Edita localização'),
            array('name' => 'localizacao_delete', 'description' => 'Deleta localização'),

            array('name' => 'transferencia_estoque_view', 'description' => 'Visualiza transferência de estoque'),
            array('name' => 'transferencia_estoque_create', 'description' => 'Cria transferência de estoque'),
            array('name' => 'transferencia_estoque_delete', 'description' => 'Deleta transferência de estoque'),

            array('name' => 'config_reserva_view', 'description' => 'Visualiza configuração de reserva'),

            array('name' => 'categoria_acomodacao_view', 'description' => 'Visualiza categoria de acomodação'),
            array('name' => 'categoria_acomodacao_create', 'description' => 'Cria categoria de acomodação'),
            array('name' => 'categoria_acomodacao_edit', 'description' => 'Edita categoria de acomodação'),
            array('name' => 'categoria_acomodacao_delete', 'description' => 'Deleta categoria de acomodação'),

            array('name' => 'acomodacao_view', 'description' => 'Visualiza acomodação'),
            array('name' => 'acomodacao_create', 'description' => 'Cria acomodação'),
            array('name' => 'acomodacao_edit', 'description' => 'Edita acomodação'),
            array('name' => 'acomodacao_delete', 'description' => 'Deleta acomodação'),

            array('name' => 'frigobar_view', 'description' => 'Visualiza frigobar'),
            array('name' => 'frigobar_create', 'description' => 'Cria frigobar'),
            array('name' => 'frigobar_edit', 'description' => 'Edita frigobar'),
            array('name' => 'frigobar_delete', 'description' => 'Deleta frigobar'),

            array('name' => 'reserva_view', 'description' => 'Visualiza reserva'),
            array('name' => 'reserva_create', 'description' => 'Cria reserva'),
            array('name' => 'reserva_edit', 'description' => 'Edita reserva'),
            array('name' => 'reserva_delete', 'description' => 'Deleta reserva'),

            array('name' => 'troca_view', 'description' => 'Visualiza troca'),
            array('name' => 'troca_create', 'description' => 'Cria troca'),
            array('name' => 'troca_delete', 'description' => 'Deleta troca'),

            array('name' => 'contigencia_view', 'description' => 'Visualiza contigência'),
            array('name' => 'contigencia_create', 'description' => 'Cria contigência'),

            array('name' => 'woocommerce_view', 'description' => 'Visualiza woocommerce'),
            array('name' => 'config_tef_view', 'description' => 'Visualiza configuração TEF'),
            array('name' => 'config_api', 'description' => 'Visualiza configuração API'),

            array('name' => 'comissao_margem_view', 'description' => 'Visualiza comissão margem'),
            array('name' => 'comissao_margem_create', 'description' => 'Cria comissão margem'),
            array('name' => 'comissao_margem_edit', 'description' => 'Edita comissão margem'),
            array('name' => 'comissao_margem_delete', 'description' => 'Deleta comissão margem'),

            array('name' => 'unidade_medida_view', 'description' => 'Visualiza unidade de medida'),
            array('name' => 'unidade_medida_create', 'description' => 'Cria unidade de medida'),
            array('name' => 'unidade_medida_edit', 'description' => 'Edita unidade de medida'),
            array('name' => 'unidade_medida_delete', 'description' => 'Deleta unidade de medida'),

            array('name' => 'tipo_despesa_frete_view', 'description' => 'Visualiza tipos de despesa frete'),
            array('name' => 'tipo_despesa_frete_create', 'description' => 'Cria tipos de despesa frete'),
            array('name' => 'tipo_despesa_frete_edit', 'description' => 'Edita tipos de despesa frete'),
            array('name' => 'tipo_despesa_frete_delete', 'description' => 'Deleta tipos de despesa frete'),

            array('name' => 'frete_view', 'description' => 'Visualiza frete'),
            array('name' => 'frete_create', 'description' => 'Cria frete'),
            array('name' => 'frete_edit', 'description' => 'Edita frete'),
            array('name' => 'frete_delete', 'description' => 'Deleta frete'),

            array('name' => 'manutencao_veiculo_view', 'description' => 'Visualiza manutenção de veículos'),
            array('name' => 'manutencao_veiculo_create', 'description' => 'Cria manutenção de veículos'),
            array('name' => 'manutencao_veiculo_edit', 'description' => 'Edita manutenção de veículos'),
            array('name' => 'manutencao_veiculo_delete', 'description' => 'Deleta manutenção de veículos'),

            array('name' => 'email_config_view', 'description' => 'Visualiza configuração de email'),
            array('name' => 'escritorio_contabil_view', 'description' => 'Visualiza escritório contábil'),

            array('name' => 'sped_config_view', 'description' => 'Visualiza configuração de sped'),
            array('name' => 'sped_create', 'description' => 'Cria sped'),

            array('name' => 'relacao_dados_fornecedor_view', 'description' => 'Visualiza relação dados fornecedor'),
            array('name' => 'relacao_dados_fornecedor_create', 'description' => 'Cria relação dados fornecedor'),
            array('name' => 'relacao_dados_fornecedor_edit', 'description' => 'Edita relação dados fornecedor'),
            array('name' => 'relacao_dados_fornecedor_delete', 'description' => 'Deleta relação dados fornecedor'),

            array('name' => 'inventario_view', 'description' => 'Visualiza inventário'),
            array('name' => 'inventario_create', 'description' => 'Cria inventário'),
            array('name' => 'inventario_edit', 'description' => 'Edita inventário'),
            array('name' => 'inventario_delete', 'description' => 'Deleta inventário'),

            array('name' => 'convenio_view', 'description' => 'Visualiza convênio'),
            array('name' => 'convenio_create', 'description' => 'Cria convênio'),
            array('name' => 'convenio_edit', 'description' => 'Edita convênio'),
            array('name' => 'convenio_delete', 'description' => 'Deleta convênio'),

            array('name' => 'medico_view', 'description' => 'Visualiza médico'),
            array('name' => 'medico_create', 'description' => 'Cria médico'),
            array('name' => 'medico_edit', 'description' => 'Edita médico'),
            array('name' => 'medico_delete', 'description' => 'Deleta médico'),

            array('name' => 'laboratorio_view', 'description' => 'Visualiza laboratório'),
            array('name' => 'laboratorio_create', 'description' => 'Cria laboratório'),
            array('name' => 'laboratorio_edit', 'description' => 'Edita laboratório'),
            array('name' => 'laboratorio_delete', 'description' => 'Deleta laboratório'),

            array('name' => 'tratamento_otica_view', 'description' => 'Visualiza tratamento ótica'),
            array('name' => 'tratamento_otica_create', 'description' => 'Cria tratamento ótica'),
            array('name' => 'tratamento_otica_edit', 'description' => 'Edita tratamento ótica'),
            array('name' => 'tratamento_otica_delete', 'description' => 'Deleta tratamento ótica'),

            array('name' => 'formato_armacao_view', 'description' => 'Visualiza formato armação'),
            array('name' => 'formato_armacao_create', 'description' => 'Cria formato armação'),
            array('name' => 'formato_armacao_edit', 'description' => 'Edita formato armação'),
            array('name' => 'formato_armacao_delete', 'description' => 'Deleta formato armação'),

            array('name' => 'config_fiscal_usuario_view', 'description' => 'Visualiza configuração fiscal do usuário'),
            array('name' => 'config_fiscal_usuario_create', 'description' => 'Cria configuração fiscal do usuário'),
            array('name' => 'config_fiscal_usuario_edit', 'description' => 'Edita configuração fiscal do usuário'),
            array('name' => 'config_fiscal_usuario_delete', 'description' => 'Deleta configuração fiscal do usuário'),

            array('name' => 'metas_view', 'description' => 'Visualiza metas'),
            array('name' => 'metas_create', 'description' => 'Cria metas'),
            array('name' => 'metas_edit', 'description' => 'Edita metas'),
            array('name' => 'metas_delete', 'description' => 'Deleta metas'),

            array('name' => 'crm_view', 'description' => 'Visualiza crm'),
            array('name' => 'crm_create', 'description' => 'Cria crm'),
            array('name' => 'crm_edit', 'description' => 'Edita crm'),
            array('name' => 'crm_delete', 'description' => 'Deleta crm'),

            array('name' => 'plano_contas_view', 'description' => 'Visualiza plano de contas'),
            array('name' => 'ifood_view', 'description' => 'Visualiza ifood'),

            array('name' => 'impressora_pedido_view', 'description' => 'Visualiza impressora pedido'),
            array('name' => 'impressora_pedido_create', 'description' => 'Cria impressora pedido'),
            array('name' => 'impressora_pedido_edit', 'description' => 'Edita impressora pedido'),
            array('name' => 'impressora_pedido_delete', 'description' => 'Deleta impressora pedido'),

            array('name' => 'mesa_view', 'description' => 'Visualiza mesa'),
            array('name' => 'mesa_create', 'description' => 'Cria mesa'),
            array('name' => 'mesa_edit', 'description' => 'Edita mesa'),
            array('name' => 'mesa_delete', 'description' => 'Deleta mesa'),

            array('name' => 'categoria_conta_view', 'description' => 'Visualiza categoria de conta'),
            array('name' => 'categoria_conta_create', 'description' => 'Cria categoria de conta'),
            array('name' => 'categoria_conta_edit', 'description' => 'Edita categoria de conta'),
            array('name' => 'categoria_conta_delete', 'description' => 'Deleta categoria de conta'),

            array('name' => 'planejamento_custo_view', 'description' => 'Visualiza planejamento de custos'),
            array('name' => 'planejamento_custo_create', 'description' => 'Cria planejamento de custos'),
            array('name' => 'planejamento_custo_edit', 'description' => 'Edita planejamento de custos'),
            array('name' => 'planejamento_custo_delete', 'description' => 'Deleta planejamento de custos'),

            array('name' => 'projeto_custo_view', 'description' => 'Visualiza projeto de custos'),
            array('name' => 'projeto_custo_create', 'description' => 'Cria projeto de custos'),
            array('name' => 'projeto_custo_edit', 'description' => 'Edita projeto de custos'),
            array('name' => 'projeto_custo_delete', 'description' => 'Deleta projeto de custos'),

            array('name' => 'promocao_produtos_view', 'description' => 'Visualiza promoção de produtos'),
            array('name' => 'promocao_produtos_create', 'description' => 'Cria promoção de produtos'),
            array('name' => 'promocao_produtos_edit', 'description' => 'Edita promoção de produtos'),
            array('name' => 'promocao_produtos_delete', 'description' => 'Deleta promoção de produtos'),

            array('name' => 'gestao_producao_view', 'description' => 'Deleta gestão de custos'),
            array('name' => 'gestao_producao_create', 'description' => 'Cria gestão de custos'),
            array('name' => 'gestao_producao_edit', 'description' => 'Edita gestão de custos'),
            array('name' => 'gestao_producao_delete', 'description' => 'Deleta gestão de custos'),

            array('name' => 'garantias_view', 'description' => 'Deleta garantias'),
            array('name' => 'garantias_create', 'description' => 'Cria garantias'),
            array('name' => 'garantias_edit', 'description' => 'Edita garantias'),
            array('name' => 'garantias_delete', 'description' => 'Deleta garantias'),
        ];
    }
}
