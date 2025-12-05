CREATE TABLE `acesso_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `ip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `acesso_logs_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `acesso_logs_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `motivo_interrupcaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `motivo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motivo_interrupcaos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `motivo_interrupcaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `evento_salarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('semanal','mensal','anual') COLLATE utf8mb4_unicode_ci NOT NULL,
  `metodo` enum('informado','fixo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `condicao` enum('soma','diminui') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_valor` enum('fixo','percentual') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `empresa_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evento_salarios_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `evento_salarios_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `apuracao_mensals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `funcionario_id` bigint unsigned NOT NULL,
  `mes` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ano` int NOT NULL,
  `valor_final` decimal(10,2) NOT NULL,
  `forma_pagamento` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conta_pagar_id` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `apuracao_mensals_funcionario_id_foreign` (`funcionario_id`),
  CONSTRAINT `apuracao_mensals_funcionario_id_foreign` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `apuracao_mensal_eventos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `apuracao_id` bigint unsigned DEFAULT NULL,
  `evento_id` bigint unsigned DEFAULT NULL,
  `valor` decimal(8,2) NOT NULL,
  `metodo` enum('informado','fixo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `condicao` enum('soma','diminui') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `apuracao_mensal_eventos_apuracao_id_foreign` (`apuracao_id`),
  KEY `apuracao_mensal_eventos_evento_id_foreign` (`evento_id`),
  CONSTRAINT `apuracao_mensal_eventos_apuracao_id_foreign` FOREIGN KEY (`apuracao_id`) REFERENCES `apuracao_mensals` (`id`),
  CONSTRAINT `apuracao_mensal_eventos_evento_id_foreign` FOREIGN KEY (`evento_id`) REFERENCES `evento_salarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `funcionario_eventos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `funcionario_id` bigint unsigned NOT NULL,
  `evento_id` bigint unsigned DEFAULT NULL,
  `condicao` enum('soma','diminui') COLLATE utf8mb4_unicode_ci NOT NULL,
  `metodo` enum('informado','fixo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `funcionario_eventos_funcionario_id_foreign` (`funcionario_id`),
  KEY `funcionario_eventos_evento_id_foreign` (`evento_id`),
  CONSTRAINT `funcionario_eventos_evento_id_foreign` FOREIGN KEY (`evento_id`) REFERENCES `evento_salarios` (`id`),
  CONSTRAINT `funcionario_eventos_funcionario_id_foreign` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bairro_delivery_masters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidade_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bairro_delivery_masters_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `bairro_delivery_masters_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bairro_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_entrega` decimal(10,2) NOT NULL,
  `bairro_delivery_super` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `bairro_deliveries_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `bairro_deliveries_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contador_empresas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `contador_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contador_empresas_empresa_id_foreign` (`empresa_id`),
  KEY `contador_empresas_contador_id_foreign` (`contador_id`),
  CONSTRAINT `contador_empresas_contador_id_foreign` FOREIGN KEY (`contador_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `contador_empresas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `plano_pendentes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `contador_id` bigint unsigned NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `plano_id` bigint unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plano_pendentes_empresa_id_foreign` (`empresa_id`),
  KEY `plano_pendentes_contador_id_foreign` (`contador_id`),
  KEY `plano_pendentes_plano_id_foreign` (`plano_id`),
  CONSTRAINT `plano_pendentes_contador_id_foreign` FOREIGN KEY (`contador_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `plano_pendentes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `plano_pendentes_plano_id_foreign` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `difals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `uf` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cfop` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pICMSUFDest` decimal(6,2) NOT NULL,
  `pICMSInter` decimal(6,2) NOT NULL,
  `pICMSInterPart` decimal(6,2) NOT NULL,
  `pFCPUFDest` decimal(6,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `difals_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `difals_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `financeiro_contadors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contador_id` bigint unsigned DEFAULT NULL,
  `percentual_comissao` decimal(5,2) NOT NULL,
  `valor_comissao` decimal(10,2) NOT NULL,
  `total_venda` decimal(10,2) NOT NULL,
  `mes` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ano` int NOT NULL,
  `tipo_pagamento` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pagamento` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financeiro_contadors_contador_id_foreign` (`contador_id`),
  CONSTRAINT `financeiro_contadors_contador_id_foreign` FOREIGN KEY (`contador_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `market_place_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `cidade_id` bigint unsigned DEFAULT NULL,
  `link_facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rua` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bairro` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tempo_medio_entrega` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_entrega` decimal(10,2) DEFAULT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_entrega_gratis` int DEFAULT NULL,
  `usar_bairros` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `notificacao_novo_pedido` tinyint(1) NOT NULL DEFAULT '1',
  `mercadopago_public_key` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mercadopago_access_token` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_divisao_pizza` enum('divide','valor_maior') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'divide',
  `logo` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fav_icon` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipos_pagamento` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '[]',
  `segmento` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '[]',
  `pedido_minimo` decimal(10,2) DEFAULT NULL,
  `avaliacao_media` decimal(10,2) NOT NULL,
  `api_token` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autenticacao_sms` tinyint(1) NOT NULL DEFAULT '0',
  `confirmacao_pedido_cliente` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipo_entrega` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `market_place_configs_empresa_id_foreign` (`empresa_id`),
  KEY `market_place_configs_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `market_place_configs_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `market_place_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `destaque_market_places` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `servico_id` bigint unsigned DEFAULT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` decimal(12,4) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `imagem` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `destaque_market_places_empresa_id_foreign` (`empresa_id`),
  KEY `destaque_market_places_produto_id_foreign` (`produto_id`),
  KEY `destaque_market_places_servico_id_foreign` (`servico_id`),
  CONSTRAINT `destaque_market_places_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `destaque_market_places_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `destaque_market_places_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cupom_descontos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `tipo_desconto` enum('valor','percentual') COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` decimal(10,4) NOT NULL,
  `valor_minimo_pedido` decimal(12,4) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `expiracao` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cupom_descontos_empresa_id_foreign` (`empresa_id`),
  KEY `cupom_descontos_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `cupom_descontos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `cupom_descontos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `funcionamento_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `inicio` time NOT NULL,
  `fim` time NOT NULL,
  `dia` enum('segunda','terca','quarta','quinta','sexta','sabado','domingo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `funcionamento_deliveries_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `funcionamento_deliveries_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `motoboys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_comissao` decimal(10,2) NOT NULL,
  `tipo_comissao` enum('valor_fixo','percentual') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motoboys_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `motoboys_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `endereco_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cidade_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `bairro_id` bigint unsigned NOT NULL,
  `rua` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('casa','trabalho') COLLATE utf8mb4_unicode_ci NOT NULL,
  `padrao` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `endereco_deliveries_cidade_id_foreign` (`cidade_id`),
  KEY `endereco_deliveries_cliente_id_foreign` (`cliente_id`),
  KEY `endereco_deliveries_bairro_id_foreign` (`bairro_id`),
  CONSTRAINT `endereco_deliveries_bairro_id_foreign` FOREIGN KEY (`bairro_id`) REFERENCES `bairro_deliveries` (`id`),
  CONSTRAINT `endereco_deliveries_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `endereco_deliveries_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pedido_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `motoboy_id` bigint unsigned DEFAULT NULL,
  `comissao_motoboy` decimal(10,2) DEFAULT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `troco_para` decimal(10,2) DEFAULT NULL,
  `tipo_pagamento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('novo','aprovado','cancelado','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `motivo_estado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_id` bigint unsigned DEFAULT NULL,
  `cupom_id` bigint unsigned DEFAULT NULL,
  `desconto` decimal(10,2) DEFAULT NULL,
  `valor_entrega` decimal(10,2) NOT NULL,
  `app` tinyint(1) NOT NULL,
  `qr_code_base64` text COLLATE utf8mb4_unicode_ci,
  `qr_code` text COLLATE utf8mb4_unicode_ci,
  `transacao_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pagamento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pedido_lido` tinyint(1) NOT NULL DEFAULT '0',
  `finalizado` tinyint(1) NOT NULL DEFAULT '0',
  `horario_cricao` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `horario_leitura` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `horario_entrega` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nfce_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_deliveries_empresa_id_foreign` (`empresa_id`),
  KEY `pedido_deliveries_cliente_id_foreign` (`cliente_id`),
  KEY `pedido_deliveries_motoboy_id_foreign` (`motoboy_id`),
  KEY `pedido_deliveries_endereco_id_foreign` (`endereco_id`),
  KEY `pedido_deliveries_cupom_id_foreign` (`cupom_id`),
  CONSTRAINT `pedido_deliveries_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `pedido_deliveries_cupom_id_foreign` FOREIGN KEY (`cupom_id`) REFERENCES `cupom_descontos` (`id`),
  CONSTRAINT `pedido_deliveries_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `pedido_deliveries_endereco_id_foreign` FOREIGN KEY (`endereco_id`) REFERENCES `endereco_deliveries` (`id`),
  CONSTRAINT `pedido_deliveries_motoboy_id_foreign` FOREIGN KEY (`motoboy_id`) REFERENCES `motoboys` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_pedido_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned NOT NULL,
  `pedido_id` bigint unsigned NOT NULL,
  `tamanho_id` bigint unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `estado` enum('novo','pendente','preparando','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'novo',
  `quantidade` decimal(8,2) NOT NULL,
  `valor_unitario` decimal(12,2) NOT NULL,
  `sub_total` decimal(12,2) NOT NULL,
  `observacao` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_pedido_deliveries_produto_id_foreign` (`produto_id`),
  KEY `item_pedido_deliveries_pedido_id_foreign` (`pedido_id`),
  KEY `item_pedido_deliveries_tamanho_id_foreign` (`tamanho_id`),
  CONSTRAINT `item_pedido_deliveries_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedido_deliveries` (`id`),
  CONSTRAINT `item_pedido_deliveries_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `item_pedido_deliveries_tamanho_id_foreign` FOREIGN KEY (`tamanho_id`) REFERENCES `tamanho_pizzas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_adicional_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_pedido_id` bigint unsigned NOT NULL,
  `adicional_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_adicional_deliveries_item_pedido_id_foreign` (`item_pedido_id`),
  KEY `item_adicional_deliveries_adicional_id_foreign` (`adicional_id`),
  CONSTRAINT `item_adicional_deliveries_adicional_id_foreign` FOREIGN KEY (`adicional_id`) REFERENCES `adicionals` (`id`),
  CONSTRAINT `item_adicional_deliveries_item_pedido_id_foreign` FOREIGN KEY (`item_pedido_id`) REFERENCES `item_pedido_deliveries` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_pizza_pedido_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_pedido_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_pizza_pedido_deliveries_item_pedido_id_foreign` (`item_pedido_id`),
  KEY `item_pizza_pedido_deliveries_produto_id_foreign` (`produto_id`),
  CONSTRAINT `item_pizza_pedido_deliveries_item_pedido_id_foreign` FOREIGN KEY (`item_pedido_id`) REFERENCES `item_pedido_deliveries` (`id`),
  CONSTRAINT `item_pizza_pedido_deliveries_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `motoboy_comissaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `pedido_id` bigint unsigned NOT NULL,
  `motoboy_id` bigint unsigned NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `valor_total_pedido` decimal(10,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motoboy_comissaos_empresa_id_foreign` (`empresa_id`),
  KEY `motoboy_comissaos_pedido_id_foreign` (`pedido_id`),
  KEY `motoboy_comissaos_motoboy_id_foreign` (`motoboy_id`),
  CONSTRAINT `motoboy_comissaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `motoboy_comissaos_motoboy_id_foreign` FOREIGN KEY (`motoboy_id`) REFERENCES `motoboys` (`id`),
  CONSTRAINT `motoboy_comissaos_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedido_deliveries` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cupom_desconto_clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned NOT NULL,
  `empresa_id` bigint unsigned NOT NULL,
  `cupom_id` bigint unsigned NOT NULL,
  `pedido_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cupom_desconto_clientes_cliente_id_foreign` (`cliente_id`),
  KEY `cupom_desconto_clientes_empresa_id_foreign` (`empresa_id`),
  KEY `cupom_desconto_clientes_cupom_id_foreign` (`cupom_id`),
  KEY `cupom_desconto_clientes_pedido_id_foreign` (`pedido_id`),
  CONSTRAINT `cupom_desconto_clientes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `cupom_desconto_clientes_cupom_id_foreign` FOREIGN KEY (`cupom_id`) REFERENCES `cupom_descontos` (`id`),
  CONSTRAINT `cupom_desconto_clientes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `cupom_desconto_clientes_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedido_deliveries` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cash_back_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `valor_percentual` decimal(5,2) NOT NULL,
  `dias_expiracao` int NOT NULL,
  `valor_minimo_venda` decimal(10,2) NOT NULL,
  `percentual_maximo_venda` decimal(10,2) NOT NULL,
  `mensagem_padrao_whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_back_configs_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `cash_back_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cash_back_clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `tipo` enum('venda','pdv') COLLATE utf8mb4_unicode_ci NOT NULL,
  `venda_id` int NOT NULL,
  `valor_venda` decimal(16,7) NOT NULL,
  `valor_credito` decimal(16,7) NOT NULL,
  `valor_percentual` decimal(5,2) NOT NULL,
  `data_expiracao` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_back_clientes_empresa_id_foreign` (`empresa_id`),
  KEY `cash_back_clientes_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `cash_back_clientes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `cash_back_clientes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `variacao_modelos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `descricao` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `empresa_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `variacao_modelos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `variacao_modelos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `variacao_modelo_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `variacao_modelo_id` bigint unsigned NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `variacao_modelo_items_variacao_modelo_id_foreign` (`variacao_modelo_id`),
  CONSTRAINT `variacao_modelo_items_variacao_modelo_id_foreign` FOREIGN KEY (`variacao_modelo_id`) REFERENCES `variacao_modelos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produto_variacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned DEFAULT NULL,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(12,4) NOT NULL,
  `codigo_barras` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagem` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_variacaos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `produto_variacaos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ecommerce_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loja_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao_breve` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rua` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bairro` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cep` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidade_id` bigint unsigned NOT NULL,
  `telefone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_facebook` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_whatsapp` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_instagram` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frete_gratis_valor` decimal(10,2) DEFAULT NULL,
  `mercadopago_public_key` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mercadopago_access_token` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `habilitar_retirada` tinyint(1) NOT NULL DEFAULT '0',
  `notificacao_novo_pedido` tinyint(1) NOT NULL DEFAULT '1',
  `desconto_padrao_boleto` decimal(4,2) DEFAULT NULL,
  `desconto_padrao_pix` decimal(4,2) DEFAULT NULL,
  `desconto_padrao_cartao` decimal(4,2) DEFAULT NULL,
  `tipos_pagamento` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '[]',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `politica_privacidade` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `termos_condicoes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ecommerce_configs_empresa_id_foreign` (`empresa_id`),
  KEY `ecommerce_configs_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `ecommerce_configs_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `ecommerce_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `galeria_produtos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned NOT NULL,
  `imagem` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `galeria_produtos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `galeria_produtos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `endereco_ecommerces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cidade_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `rua` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bairro` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `padrao` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `endereco_ecommerces_cidade_id_foreign` (`cidade_id`),
  KEY `endereco_ecommerces_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `endereco_ecommerces_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `endereco_ecommerces_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `carrinhos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `empresa_id` bigint unsigned NOT NULL,
  `endereco_id` bigint unsigned DEFAULT NULL,
  `estado` enum('pendente','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `tipo_frete` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_frete` decimal(10,2) NOT NULL,
  `cep` decimal(9,2) NOT NULL,
  `session_cart` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrinhos_cliente_id_foreign` (`cliente_id`),
  KEY `carrinhos_empresa_id_foreign` (`empresa_id`),
  KEY `carrinhos_endereco_id_foreign` (`endereco_id`),
  CONSTRAINT `carrinhos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `carrinhos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `carrinhos_endereco_id_foreign` FOREIGN KEY (`endereco_id`) REFERENCES `endereco_ecommerces` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_carrinhos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `carrinho_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `variacao_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(8,3) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,3) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_carrinhos_carrinho_id_foreign` (`carrinho_id`),
  KEY `item_carrinhos_produto_id_foreign` (`produto_id`),
  KEY `item_carrinhos_variacao_id_foreign` (`variacao_id`),
  CONSTRAINT `item_carrinhos_carrinho_id_foreign` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinhos` (`id`),
  CONSTRAINT `item_carrinhos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `item_carrinhos_variacao_id_foreign` FOREIGN KEY (`variacao_id`) REFERENCES `produto_variacaos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pedido_ecommerces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned NOT NULL,
  `empresa_id` bigint unsigned NOT NULL,
  `endereco_id` bigint unsigned DEFAULT NULL,
  `estado` enum('novo','preparando','em_trasporte','finalizado','recusado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_pagamento` enum('cartao','pix','boleto') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `valor_frete` decimal(10,2) NOT NULL,
  `desconto` decimal(10,2) NOT NULL,
  `tipo_frete` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rua_entrega` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_entrega` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_entrega` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro_entrega` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep_entrega` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade_entrega` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_boleto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `qr_code_base64` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `qr_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_pedido` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_pagamento` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `transacao_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `nfe_id` int DEFAULT NULL,
  `cupom_desconto` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_entrega` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_rastreamento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pedido_lido` tinyint(1) NOT NULL DEFAULT '0',
  `nome` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sobre_nome` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_documento` enum('cpf','cnpj') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_documento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_ecommerces_cliente_id_foreign` (`cliente_id`),
  KEY `pedido_ecommerces_empresa_id_foreign` (`empresa_id`),
  KEY `pedido_ecommerces_endereco_id_foreign` (`endereco_id`),
  CONSTRAINT `pedido_ecommerces_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `pedido_ecommerces_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `pedido_ecommerces_endereco_id_foreign` FOREIGN KEY (`endereco_id`) REFERENCES `endereco_ecommerces` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_pedido_ecommerces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `variacao_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(8,3) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_pedido_ecommerces_pedido_id_foreign` (`pedido_id`),
  KEY `item_pedido_ecommerces_produto_id_foreign` (`produto_id`),
  KEY `item_pedido_ecommerces_variacao_id_foreign` (`variacao_id`),
  CONSTRAINT `item_pedido_ecommerces_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedido_ecommerces` (`id`),
  CONSTRAINT `item_pedido_ecommerces_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `item_pedido_ecommerces_variacao_id_foreign` FOREIGN KEY (`variacao_id`) REFERENCES `produto_variacaos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cotacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `fornecedor_id` bigint unsigned NOT NULL,
  `responsavel` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_link` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao_resposta` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `valor_total` decimal(10,2) DEFAULT NULL,
  `desconto` decimal(10,2) DEFAULT NULL,
  `estado` enum('nova','respondida','aprovada','rejeitada') COLLATE utf8mb4_unicode_ci NOT NULL,
  `escolhida` tinyint(1) NOT NULL DEFAULT '0',
  `data_resposta` timestamp NULL DEFAULT NULL,
  `nfe_id` int DEFAULT NULL,
  `valor_frete` decimal(10,2) DEFAULT NULL,
  `observacao_frete` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previsao_entrega` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cotacaos_empresa_id_foreign` (`empresa_id`),
  KEY `cotacaos_fornecedor_id_foreign` (`fornecedor_id`),
  CONSTRAINT `cotacaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `cotacaos_fornecedor_id_foreign` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_cotacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cotacao_id` bigint unsigned DEFAULT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `valor_unitario` decimal(12,3) DEFAULT NULL,
  `quantidade` decimal(12,3) NOT NULL,
  `sub_total` decimal(12,3) DEFAULT NULL,
  `observacao` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_cotacaos_cotacao_id_foreign` (`cotacao_id`),
  KEY `item_cotacaos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `item_cotacaos_cotacao_id_foreign` FOREIGN KEY (`cotacao_id`) REFERENCES `cotacaos` (`id`),
  CONSTRAINT `item_cotacaos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fatura_cotacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cotacao_id` bigint unsigned DEFAULT NULL,
  `tipo_pagamento` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_vencimento` date NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fatura_cotacaos_cotacao_id_foreign` (`cotacao_id`),
  CONSTRAINT `fatura_cotacaos_cotacao_id_foreign` FOREIGN KEY (`cotacao_id`) REFERENCES `cotacaos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `nota_servicos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `cidade_id` bigint unsigned DEFAULT NULL,
  `valor_total` decimal(16,7) NOT NULL,
  `estado` enum('novo','rejeitado','aprovado','cancelado','processando') COLLATE utf8mb4_unicode_ci NOT NULL,
  `serie` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_verificacao` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_nfse` int NOT NULL,
  `url_xml` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_pdf_nfse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_pdf_rps` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razao_social` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `im` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ie` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rua` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bairro` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `complemento` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `natureza_operacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uuid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chave` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ambiente` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nota_servicos_empresa_id_foreign` (`empresa_id`),
  KEY `nota_servicos_cliente_id_foreign` (`cliente_id`),
  KEY `nota_servicos_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `nota_servicos_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `nota_servicos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `nota_servicos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_nota_servicos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nota_servico_id` bigint unsigned DEFAULT NULL,
  `servico_id` bigint unsigned DEFAULT NULL,
  `discriminacao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_servico` decimal(16,7) NOT NULL,
  `codigo_cnae` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_servico` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_tributacao_municipio` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exigibilidade_iss` int NOT NULL,
  `iss_retido` int NOT NULL,
  `data_competencia` date DEFAULT NULL,
  `estado_local_prestacao_servico` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade_local_prestacao_servico` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_deducoes` decimal(16,7) DEFAULT NULL,
  `desconto_incondicional` decimal(16,7) DEFAULT NULL,
  `desconto_condicional` decimal(16,7) DEFAULT NULL,
  `outras_retencoes` decimal(16,7) DEFAULT NULL,
  `aliquota_iss` decimal(7,2) DEFAULT NULL,
  `aliquota_pis` decimal(7,2) DEFAULT NULL,
  `aliquota_cofins` decimal(7,2) DEFAULT NULL,
  `aliquota_inss` decimal(7,2) DEFAULT NULL,
  `aliquota_ir` decimal(7,2) DEFAULT NULL,
  `aliquota_csll` decimal(7,2) DEFAULT NULL,
  `intermediador` enum('n','f','j') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documento_intermediador` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome_intermediador` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `im_intermediador` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsavel_retencao_iss` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_nota_servicos_nota_servico_id_foreign` (`nota_servico_id`),
  KEY `item_nota_servicos_servico_id_foreign` (`servico_id`),
  CONSTRAINT `item_nota_servicos_nota_servico_id_foreign` FOREIGN KEY (`nota_servico_id`) REFERENCES `nota_servicos` (`id`),
  CONSTRAINT `item_nota_servicos_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lista_precos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ajuste_sobre` enum('valor_compra','valor_venda') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('incremento','reducao') COLLATE utf8mb4_unicode_ci NOT NULL,
  `percentual_alteracao` decimal(5,2) NOT NULL,
  `tipo_pagamento` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `funcionario_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lista_precos_empresa_id_foreign` (`empresa_id`),
  KEY `lista_precos_funcionario_id_foreign` (`funcionario_id`),
  CONSTRAINT `lista_precos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `lista_precos_funcionario_id_foreign` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_lista_precos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lista_id` bigint unsigned DEFAULT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `percentual_lucro` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_lista_precos_lista_id_foreign` (`lista_id`),
  KEY `item_lista_precos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `item_lista_precos_lista_id_foreign` FOREIGN KEY (`lista_id`) REFERENCES `lista_precos` (`id`),
  CONSTRAINT `item_lista_precos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ncms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `assunto` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departamento` enum('financeiro','suporte') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('aberto','respondida','resolvido','aguardando') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tickets_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `tickets_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ticket_mensagems` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `resposta` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_mensagems_ticket_id_foreign` (`ticket_id`),
  CONSTRAINT `ticket_mensagems_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ticket_mensagem_anexos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_mensagem_id` bigint unsigned NOT NULL,
  `anexo` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_mensagem_anexos_ticket_mensagem_id_foreign` (`ticket_mensagem_id`),
  CONSTRAINT `ticket_mensagem_anexos_ticket_mensagem_id_foreign` FOREIGN KEY (`ticket_mensagem_id`) REFERENCES `ticket_mensagems` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notificacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `tabela` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao_curta` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `visualizada` tinyint(1) NOT NULL DEFAULT '0',
  `por_sistema` tinyint(1) NOT NULL DEFAULT '0',
  `prioridade` enum('baixa','media','alta') COLLATE utf8mb4_unicode_ci NOT NULL,
  `super` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notificacaos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `notificacaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `carrinho_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `empresa_id` bigint unsigned NOT NULL,
  `endereco_id` bigint unsigned DEFAULT NULL,
  `estado` enum('pendente','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `valor_desconto` decimal(10,2) NOT NULL,
  `cupom` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_frete` decimal(10,2) NOT NULL,
  `session_cart_delivery` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrinho_deliveries_cliente_id_foreign` (`cliente_id`),
  KEY `carrinho_deliveries_empresa_id_foreign` (`empresa_id`),
  KEY `carrinho_deliveries_endereco_id_foreign` (`endereco_id`),
  CONSTRAINT `carrinho_deliveries_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `carrinho_deliveries_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `carrinho_deliveries_endereco_id_foreign` FOREIGN KEY (`endereco_id`) REFERENCES `endereco_deliveries` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_carrinho_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `carrinho_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `tamanho_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(8,3) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,3) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_carrinho_deliveries_carrinho_id_foreign` (`carrinho_id`),
  KEY `item_carrinho_deliveries_produto_id_foreign` (`produto_id`),
  KEY `item_carrinho_deliveries_tamanho_id_foreign` (`tamanho_id`),
  CONSTRAINT `item_carrinho_deliveries_carrinho_id_foreign` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinho_deliveries` (`id`),
  CONSTRAINT `item_carrinho_deliveries_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `item_carrinho_deliveries_tamanho_id_foreign` FOREIGN KEY (`tamanho_id`) REFERENCES `tamanho_pizzas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ibpts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uf` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `versao` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_ibpts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ibpt_id` bigint unsigned DEFAULT NULL,
  `codigo` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nacional_federal` decimal(5,2) NOT NULL,
  `importado_federal` decimal(5,2) NOT NULL,
  `estadual` decimal(5,2) NOT NULL,
  `municipal` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_ibpts_ibpt_id_foreign` (`ibpt_id`),
  CONSTRAINT `item_ibpts_ibpt_id_foreign` FOREIGN KEY (`ibpt_id`) REFERENCES `ibpts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60636 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_carrinho_adicional_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_carrinho_id` bigint unsigned NOT NULL,
  `adicional_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_carrinho_adicional_deliveries_item_carrinho_id_foreign` (`item_carrinho_id`),
  KEY `item_carrinho_adicional_deliveries_adicional_id_foreign` (`adicional_id`),
  CONSTRAINT `item_carrinho_adicional_deliveries_adicional_id_foreign` FOREIGN KEY (`adicional_id`) REFERENCES `adicionals` (`id`),
  CONSTRAINT `item_carrinho_adicional_deliveries_item_carrinho_id_foreign` FOREIGN KEY (`item_carrinho_id`) REFERENCES `item_carrinho_deliveries` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_pizza_carrinhos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_carrinho_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_pizza_carrinhos_item_carrinho_id_foreign` (`item_carrinho_id`),
  KEY `item_pizza_carrinhos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `item_pizza_carrinhos_item_carrinho_id_foreign` FOREIGN KEY (`item_carrinho_id`) REFERENCES `item_carrinho_deliveries` (`id`),
  CONSTRAINT `item_pizza_carrinhos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `nota_servico_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razao_social` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `regime` enum('simples','normal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ie` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `im` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnae` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_prefeitura` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha_prefeitura` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rua` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bairro` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `complemento` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `cidade_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nota_servico_configs_empresa_id_foreign` (`empresa_id`),
  KEY `nota_servico_configs_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `nota_servico_configs_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `nota_servico_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `segmentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `segmento_empresas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `segmento_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `segmento_empresas_empresa_id_foreign` (`empresa_id`),
  KEY `segmento_empresas_segmento_id_foreign` (`segmento_id`),
  CONSTRAINT `segmento_empresas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `segmento_empresas_segmento_id_foreign` FOREIGN KEY (`segmento_id`) REFERENCES `segmentos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mercado_livre_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `client_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mercado_livre_configs_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `mercado_livre_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_mercado_livres` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mercado_livre_perguntas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `texto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mercado_livre_perguntas_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `mercado_livre_perguntas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pedido_mercado_livres` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `_id` bigint NOT NULL,
  `tipo_pagamento` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `valor_entrega` decimal(10,2) NOT NULL,
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seller_id` bigint NOT NULL,
  `entrega_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_pedido` timestamp NOT NULL,
  `comentario` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nfe_id` int DEFAULT NULL,
  `rua_entrega` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_entrega` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep_entrega` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro_entrega` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade_entrega` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comentario_entrega` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_rastreamento` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_nome` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_documento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_mercado_livres_empresa_id_foreign` (`empresa_id`),
  KEY `pedido_mercado_livres_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `pedido_mercado_livres_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `pedido_mercado_livres_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_pedido_mercado_livres` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `item_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condicao` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `variacao_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantidade` decimal(8,2) NOT NULL,
  `valor_unitario` decimal(12,2) NOT NULL,
  `sub_total` decimal(12,2) NOT NULL,
  `taxa_venda` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_pedido_mercado_livres_pedido_id_foreign` (`pedido_id`),
  KEY `item_pedido_mercado_livres_produto_id_foreign` (`produto_id`),
  CONSTRAINT `item_pedido_mercado_livres_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedido_mercado_livres` (`id`),
  CONSTRAINT `item_pedido_mercado_livres_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `variacao_mercado_livres` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned DEFAULT NULL,
  `_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `variacao_mercado_livres_produto_id_foreign` (`produto_id`),
  CONSTRAINT `variacao_mercado_livres_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `plano_contas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `plano_conta_id` bigint unsigned DEFAULT NULL,
  `descricao` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plano_contas_empresa_id_foreign` (`empresa_id`),
  KEY `plano_contas_plano_conta_id_foreign` (`plano_conta_id`),
  CONSTRAINT `plano_contas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `plano_contas_plano_conta_id_foreign` FOREIGN KEY (`plano_conta_id`) REFERENCES `plano_contas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `conta_empresas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banco` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agencia` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conta` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plano_conta_id` int DEFAULT NULL,
  `saldo` decimal(16,2) DEFAULT NULL,
  `saldo_inicial` decimal(16,2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conta_empresas_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `conta_empresas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ;

CREATE TABLE `item_conta_empresas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conta_id` bigint unsigned DEFAULT NULL,
  `descricao` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caixa_id` int DEFAULT NULL,
  `tipo_pagamento` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(16,2) DEFAULT NULL,
  `saldo_atual` decimal(16,2) DEFAULT NULL,
  `tipo` enum('entrada','saida') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_conta_empresas_conta_id_foreign` (`conta_id`),
  CONSTRAINT `item_conta_empresas_conta_id_foreign` FOREIGN KEY (`conta_id`) REFERENCES `conta_empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produto_combos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantidade` decimal(8,3) NOT NULL,
  `valor_compra` decimal(12,4) NOT NULL,
  `sub_total` decimal(12,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_combos_produto_id_foreign` (`produto_id`),
  KEY `produto_combos_item_id_foreign` (`item_id`),
  CONSTRAINT `produto_combos_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `produto_combos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_servico_nfces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nfce_id` bigint unsigned NOT NULL,
  `servico_id` bigint unsigned NOT NULL,
  `quantidade` decimal(6,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `observacao` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_servico_nfces_nfce_id_foreign` (`nfce_id`),
  KEY `item_servico_nfces_servico_id_foreign` (`servico_id`),
  CONSTRAINT `item_servico_nfces_nfce_id_foreign` FOREIGN KEY (`nfce_id`) REFERENCES `nfces` (`id`),
  CONSTRAINT `item_servico_nfces_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `conta_boletos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `banco` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agencia` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conta` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titular` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `padrao` tinyint(1) NOT NULL DEFAULT '0',
  `usar_logo` tinyint(1) NOT NULL DEFAULT '0',
  `documento` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rua` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bairro` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidade_id` bigint unsigned DEFAULT NULL,
  `carteira` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `convenio` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `juros` decimal(10,2) DEFAULT NULL,
  `multa` decimal(10,2) DEFAULT NULL,
  `juros_apos` int DEFAULT NULL,
  `tipo` enum('Cnab400','Cnab240') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conta_boletos_empresa_id_foreign` (`empresa_id`),
  KEY `conta_boletos_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `conta_boletos_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `conta_boletos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `boletos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `conta_boleto_id` bigint unsigned DEFAULT NULL,
  `conta_receber_id` bigint unsigned DEFAULT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_documento` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `carteira` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `convenio` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vencimento` date NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `instrucoes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linha_digitavel` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome_arquivo` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `juros` decimal(10,2) DEFAULT NULL,
  `multa` decimal(10,2) DEFAULT NULL,
  `juros_apos` int DEFAULT NULL,
  `tipo` enum('Cnab400','Cnab240') COLLATE utf8mb4_unicode_ci NOT NULL,
  `usar_logo` tinyint(1) NOT NULL DEFAULT '0',
  `posto` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_cliente` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `boletos_empresa_id_foreign` (`empresa_id`),
  KEY `boletos_conta_boleto_id_foreign` (`conta_boleto_id`),
  KEY `boletos_conta_receber_id_foreign` (`conta_receber_id`),
  CONSTRAINT `boletos_conta_boleto_id_foreign` FOREIGN KEY (`conta_boleto_id`) REFERENCES `conta_boletos` (`id`),
  CONSTRAINT `boletos_conta_receber_id_foreign` FOREIGN KEY (`conta_receber_id`) REFERENCES `conta_recebers` (`id`),
  CONSTRAINT `boletos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `remessa_boletos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome_arquivo` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conta_boleto_id` int NOT NULL,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `remessa_boletos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `remessa_boletos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `remessa_boleto_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `remessa_id` bigint unsigned DEFAULT NULL,
  `boleto_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `remessa_boleto_items_remessa_id_foreign` (`remessa_id`),
  KEY `remessa_boleto_items_boleto_id_foreign` (`boleto_id`),
  CONSTRAINT `remessa_boleto_items_boleto_id_foreign` FOREIGN KEY (`boleto_id`) REFERENCES `boletos` (`id`),
  CONSTRAINT `remessa_boleto_items_remessa_id_foreign` FOREIGN KEY (`remessa_id`) REFERENCES `remessa_boletos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `video_suportes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pagina` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_video` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_servidor` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `nuvem_shop_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `client_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `client_secret` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nuvem_shop_configs_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `nuvem_shop_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `nuvem_shop_pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `pedido_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rua` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bairro` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidade` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cep` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `valor_frete` decimal(10,2) NOT NULL,
  `desconto` decimal(10,2) NOT NULL,
  `observacao` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nfe_id` int DEFAULT NULL,
  `status_envio` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_pagamento` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `venda_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nuvem_shop_pedidos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `nuvem_shop_pedidos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `nuvem_shop_item_pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned DEFAULT NULL,
  `pedido_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(8,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nuvem_shop_item_pedidos_produto_id_foreign` (`produto_id`),
  KEY `nuvem_shop_item_pedidos_pedido_id_foreign` (`pedido_id`),
  CONSTRAINT `nuvem_shop_item_pedidos_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `nuvem_shop_pedidos` (`id`),
  CONSTRAINT `nuvem_shop_item_pedidos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_nuvem_shops` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_nuvem_shops_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `categoria_nuvem_shops_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `type_user` smallint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`),
  KEY `roles_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `roles_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `localizacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `descricao` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome_fantasia` mediumtext COLLATE utf8mb4_unicode_ci,
  `cpf_cnpj` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aut_xml` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ie` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arquivo` blob,
  `senha` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rua` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade_id` bigint unsigned DEFAULT NULL,
  `numero_ultima_nfe_producao` int DEFAULT NULL,
  `numero_ultima_nfe_homologacao` int DEFAULT NULL,
  `numero_serie_nfe` int DEFAULT NULL,
  `numero_ultima_nfce_producao` int DEFAULT NULL,
  `numero_ultima_nfce_homologacao` int DEFAULT NULL,
  `numero_serie_nfce` int DEFAULT NULL,
  `numero_ultima_cte_producao` int DEFAULT NULL,
  `numero_ultima_cte_homologacao` int DEFAULT NULL,
  `numero_serie_cte` int DEFAULT NULL,
  `numero_ultima_mdfe_producao` int DEFAULT NULL,
  `numero_ultima_mdfe_homologacao` int DEFAULT NULL,
  `numero_serie_mdfe` int DEFAULT NULL,
  `numero_ultima_nfse` int DEFAULT NULL,
  `numero_serie_nfse` int DEFAULT NULL,
  `csc` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `csc_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ambiente` int NOT NULL,
  `tributacao` enum('MEI','Simples Nacional','Regime Normal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_nfse` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `localizacaos_empresa_id_foreign` (`empresa_id`),
  KEY `localizacaos_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `localizacaos_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `localizacaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produto_localizacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned DEFAULT NULL,
  `localizacao_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_localizacaos_produto_id_foreign` (`produto_id`),
  KEY `produto_localizacaos_localizacao_id_foreign` (`localizacao_id`),
  CONSTRAINT `produto_localizacaos_localizacao_id_foreign` FOREIGN KEY (`localizacao_id`) REFERENCES `localizacaos` (`id`),
  CONSTRAINT `produto_localizacaos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuario_localizacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `localizacao_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_localizacaos_usuario_id_foreign` (`usuario_id`),
  KEY `usuario_localizacaos_localizacao_id_foreign` (`localizacao_id`),
  CONSTRAINT `usuario_localizacaos_localizacao_id_foreign` FOREIGN KEY (`localizacao_id`) REFERENCES `localizacaos` (`id`),
  CONSTRAINT `usuario_localizacaos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `financeiro_planos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `plano_id` bigint unsigned DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `tipo_pagamento` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_pagamento` enum('pendente','recebido','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `plano_empresa_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financeiro_planos_empresa_id_foreign` (`empresa_id`),
  KEY `financeiro_planos_plano_id_foreign` (`plano_id`),
  CONSTRAINT `financeiro_planos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `financeiro_planos_plano_id_foreign` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `modelo_etiquetas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `nome` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `altura` decimal(7,2) NOT NULL,
  `largura` decimal(7,2) NOT NULL,
  `etiquestas_por_linha` int NOT NULL,
  `distancia_etiquetas_lateral` decimal(7,2) NOT NULL,
  `distancia_etiquetas_topo` decimal(7,2) NOT NULL,
  `quantidade_etiquetas` int NOT NULL,
  `tamanho_fonte` decimal(7,2) NOT NULL,
  `tamanho_codigo_barras` decimal(7,2) NOT NULL,
  `nome_empresa` tinyint(1) NOT NULL,
  `nome_produto` tinyint(1) NOT NULL,
  `valor_produto` tinyint(1) NOT NULL,
  `codigo_produto` tinyint(1) NOT NULL,
  `codigo_barras_numerico` tinyint(1) NOT NULL,
  `tipo` enum('simples','gondola') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `importado_super` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `modelo_etiquetas_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `modelo_etiquetas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transferencia_estoques` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `local_saida_id` bigint unsigned DEFAULT NULL,
  `local_entrada_id` bigint unsigned DEFAULT NULL,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_transacao` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transferencia_estoques_empresa_id_foreign` (`empresa_id`),
  KEY `transferencia_estoques_local_saida_id_foreign` (`local_saida_id`),
  KEY `transferencia_estoques_local_entrada_id_foreign` (`local_entrada_id`),
  KEY `transferencia_estoques_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `transferencia_estoques_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `transferencia_estoques_local_entrada_id_foreign` FOREIGN KEY (`local_entrada_id`) REFERENCES `localizacaos` (`id`),
  CONSTRAINT `transferencia_estoques_local_saida_id_foreign` FOREIGN KEY (`local_saida_id`) REFERENCES `localizacaos` (`id`),
  CONSTRAINT `transferencia_estoques_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_transferencia_estoques` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned DEFAULT NULL,
  `transferencia_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(14,4) NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_transferencia_estoques_produto_id_foreign` (`produto_id`),
  KEY `item_transferencia_estoques_transferencia_id_foreign` (`transferencia_id`),
  CONSTRAINT `item_transferencia_estoques_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `item_transferencia_estoques_transferencia_id_foreign` FOREIGN KEY (`transferencia_id`) REFERENCES `transferencia_estoques` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reserva_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `cpf_cnpj` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razao_social` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rua` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bairro` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cep` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `complemento` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade_id` bigint unsigned NOT NULL,
  `telefone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `horario_checkin` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `horario_checkout` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reserva_configs_empresa_id_foreign` (`empresa_id`),
  KEY `reserva_configs_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `reserva_configs_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `reserva_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_acomodacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_acomodacaos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `categoria_acomodacaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `acomodacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria_id` bigint unsigned NOT NULL,
  `valor_diaria` decimal(12,2) NOT NULL,
  `capacidade` int NOT NULL,
  `estacionamento` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `acomodacaos_empresa_id_foreign` (`empresa_id`),
  KEY `acomodacaos_categoria_id_foreign` (`categoria_id`),
  CONSTRAINT `acomodacaos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categoria_acomodacaos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `acomodacaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `frigobars` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `acomodacao_id` bigint unsigned NOT NULL,
  `modelo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `frigobars_empresa_id_foreign` (`empresa_id`),
  KEY `frigobars_acomodacao_id_foreign` (`acomodacao_id`),
  CONSTRAINT `frigobars_acomodacao_id_foreign` FOREIGN KEY (`acomodacao_id`) REFERENCES `acomodacaos` (`id`),
  CONSTRAINT `frigobars_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reservas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `acomodacao_id` bigint unsigned NOT NULL,
  `data_checkin` date NOT NULL,
  `data_checkout` date NOT NULL,
  `valor_estadia` decimal(12,2) NOT NULL,
  `valor_consumo_frigobar` decimal(12,2) DEFAULT NULL,
  `valor_consumo_adicional` decimal(12,2) DEFAULT NULL,
  `desconto` decimal(12,2) DEFAULT NULL,
  `valor_outros` decimal(12,2) DEFAULT NULL,
  `valor_total` decimal(12,2) DEFAULT NULL,
  `estado` enum('pendente','iniciado','finalizado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `conferencia_frigobar` tinyint(1) NOT NULL DEFAULT '0',
  `total_hospedes` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `codigo_reseva` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_externo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_sequencial` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservas_empresa_id_foreign` (`empresa_id`),
  KEY `reservas_cliente_id_foreign` (`cliente_id`),
  KEY `reservas_acomodacao_id_foreign` (`acomodacao_id`),
  CONSTRAINT `reservas_acomodacao_id_foreign` FOREIGN KEY (`acomodacao_id`) REFERENCES `acomodacaos` (`id`),
  CONSTRAINT `reservas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `reservas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `consumo_reservas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reserva_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(8,2) NOT NULL,
  `valor_unitario` decimal(12,2) NOT NULL,
  `sub_total` decimal(12,2) NOT NULL,
  `observacao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consumo_reservas_reserva_id_foreign` (`reserva_id`),
  KEY `consumo_reservas_produto_id_foreign` (`produto_id`),
  CONSTRAINT `consumo_reservas_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `reservas` (`id`),
  CONSTRAINT `consumo_reservas_reserva_id_foreign` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notas_reservas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reserva_id` bigint unsigned NOT NULL,
  `texto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `leitura` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notas_reservas_reserva_id_foreign` (`reserva_id`),
  CONSTRAINT `notas_reservas_reserva_id_foreign` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `servico_reservas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reserva_id` bigint unsigned NOT NULL,
  `servico_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(8,2) NOT NULL,
  `valor_unitario` decimal(12,2) NOT NULL,
  `sub_total` decimal(12,2) NOT NULL,
  `observacao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `servico_reservas_reserva_id_foreign` (`reserva_id`),
  KEY `servico_reservas_servico_id_foreign` (`servico_id`),
  CONSTRAINT `servico_reservas_reserva_id_foreign` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`),
  CONSTRAINT `servico_reservas_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `padrao_frigobars` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `frigobar_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `quantidade` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `padrao_frigobars_frigobar_id_foreign` (`frigobar_id`),
  KEY `padrao_frigobars_produto_id_foreign` (`produto_id`),
  CONSTRAINT `padrao_frigobars_frigobar_id_foreign` FOREIGN KEY (`frigobar_id`) REFERENCES `frigobars` (`id`),
  CONSTRAINT `padrao_frigobars_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hospede_reservas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reserva_id` bigint unsigned NOT NULL,
  `descricao` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_completo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rua` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade_id` bigint unsigned DEFAULT NULL,
  `telefone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hospede_reservas_reserva_id_foreign` (`reserva_id`),
  KEY `hospede_reservas_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `hospede_reservas_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `hospede_reservas_reserva_id_foreign` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fatura_reservas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reserva_id` bigint unsigned DEFAULT NULL,
  `tipo_pagamento` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_vencimento` date NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fatura_reservas_reserva_id_foreign` (`reserva_id`),
  CONSTRAINT `fatura_reservas_reserva_id_foreign` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produto_fornecedors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned DEFAULT NULL,
  `fornecedor_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_fornecedors_produto_id_foreign` (`produto_id`),
  KEY `produto_fornecedors_fornecedor_id_foreign` (`fornecedor_id`),
  CONSTRAINT `produto_fornecedors_fornecedor_id_foreign` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedors` (`id`),
  CONSTRAINT `produto_fornecedors_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `venda_suspensas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `desconto` decimal(12,2) DEFAULT NULL,
  `acrescimo` decimal(12,2) DEFAULT NULL,
  `observacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_pagamento` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `local_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `funcionario_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `venda_suspensas_empresa_id_foreign` (`empresa_id`),
  KEY `venda_suspensas_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `venda_suspensas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `venda_suspensas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_venda_suspensas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `venda_id` bigint unsigned DEFAULT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `variacao_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(7,3) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_venda_suspensas_venda_id_foreign` (`venda_id`),
  KEY `item_venda_suspensas_produto_id_foreign` (`produto_id`),
  KEY `item_venda_suspensas_variacao_id_foreign` (`variacao_id`),
  CONSTRAINT `item_venda_suspensas_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `item_venda_suspensas_variacao_id_foreign` FOREIGN KEY (`variacao_id`) REFERENCES `produto_variacaos` (`id`),
  CONSTRAINT `item_venda_suspensas_venda_id_foreign` FOREIGN KEY (`venda_id`) REFERENCES `venda_suspensas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `trocas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nfce_id` bigint unsigned NOT NULL,
  `observacao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_troca` decimal(12,2) NOT NULL,
  `valor_original` decimal(12,2) NOT NULL,
  `numero_sequencial` int DEFAULT NULL,
  `codigo` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_pagamento` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trocas_empresa_id_foreign` (`empresa_id`),
  KEY `trocas_nfce_id_foreign` (`nfce_id`),
  CONSTRAINT `trocas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `trocas_nfce_id_foreign` FOREIGN KEY (`nfce_id`) REFERENCES `nfces` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_trocas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `troca_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `quantidade` decimal(7,3) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_trocas_troca_id_foreign` (`troca_id`),
  KEY `item_trocas_produto_id_foreign` (`produto_id`),
  CONSTRAINT `item_trocas_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `item_trocas_troca_id_foreign` FOREIGN KEY (`troca_id`) REFERENCES `trocas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `credito_clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credito_clientes_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `credito_clientes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contigencias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `tipo` enum('SVCAN','SVCRS','OFFLINE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_retorno` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` enum('NFe','NFCe','CTe','MDFe') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contigencias_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `contigencias_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `woocommerce_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `consumer_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consumer_secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `woocommerce_configs_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `woocommerce_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_woocommerces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_woocommerces_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `categoria_woocommerces_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `woocommerce_pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `pedido_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rua` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uf` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `valor_frete` decimal(10,2) NOT NULL,
  `desconto` decimal(10,2) NOT NULL,
  `observacao` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nfe_id` int DEFAULT NULL,
  `tipo_pagamento` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_pedido` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `venda_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `woocommerce_pedidos_empresa_id_foreign` (`empresa_id`),
  KEY `woocommerce_pedidos_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `woocommerce_pedidos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `woocommerce_pedidos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `woocommerce_item_pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `item_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` decimal(8,2) NOT NULL,
  `valor_unitario` decimal(12,2) NOT NULL,
  `sub_total` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `woocommerce_item_pedidos_pedido_id_foreign` (`pedido_id`),
  KEY `woocommerce_item_pedidos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `woocommerce_item_pedidos_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `woocommerce_pedidos` (`id`),
  CONSTRAINT `woocommerce_item_pedidos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_updates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `versao` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tef_multi_plus_cards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `usuario_id` bigint unsigned NOT NULL,
  `cnpj` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdv` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tef_multi_plus_cards_empresa_id_foreign` (`empresa_id`),
  KEY `tef_multi_plus_cards_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `tef_multi_plus_cards_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `tef_multi_plus_cards_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `registro_tefs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nfce_id` bigint unsigned DEFAULT NULL,
  `nome_rede` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nsu` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_transacao` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hora_transacao` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_total` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('aprovado','cancelado','pendente') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `registro_tefs_empresa_id_foreign` (`empresa_id`),
  KEY `registro_tefs_nfce_id_foreign` (`nfce_id`),
  CONSTRAINT `registro_tefs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `registro_tefs_nfce_id_foreign` FOREIGN KEY (`nfce_id`) REFERENCES `nfces` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `acao_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `local` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acao` enum('cadastrar','editar','excluir','transmitir','cancelar','corrigir','erro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `acao_logs_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `acao_logs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produto_unicos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nfe_id` bigint unsigned DEFAULT NULL,
  `nfce_id` bigint unsigned DEFAULT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `codigo` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('entrada','saida') COLLATE utf8mb4_unicode_ci NOT NULL,
  `em_estoque` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_unicos_nfe_id_foreign` (`nfe_id`),
  KEY `produto_unicos_nfce_id_foreign` (`nfce_id`),
  KEY `produto_unicos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `produto_unicos_nfce_id_foreign` FOREIGN KEY (`nfce_id`) REFERENCES `nfces` (`id`),
  CONSTRAINT `produto_unicos_nfe_id_foreign` FOREIGN KEY (`nfe_id`) REFERENCES `nves` (`id`),
  CONSTRAINT `produto_unicos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `api_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  `permissoes_acesso` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `api_configs_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `api_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `api_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `token` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prefixo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('sucesso','erro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('create','update','read','delete') COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `api_logs_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `api_logs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `margem_comissaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `percentual` decimal(5,2) DEFAULT NULL,
  `margem` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `margem_comissaos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `margem_comissaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `unidade_medidas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unidade_medidas_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `unidade_medidas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tipo_despesa_fretes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipo_despesa_fretes_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `tipo_despesa_fretes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `fretes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `veiculo_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `estado` enum('em_carregamento','em_viagem','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `desconto` decimal(10,2) DEFAULT NULL,
  `acrescimo` decimal(10,2) DEFAULT NULL,
  `local_id` int DEFAULT NULL,
  `cidade_id` bigint unsigned DEFAULT NULL,
  `distancia_km` decimal(10,2) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `horario_inicio` time DEFAULT NULL,
  `horario_fim` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `numero_sequencial` int DEFAULT NULL,
  `cidade_carregamento` int DEFAULT NULL,
  `cidade_descarregamento` int DEFAULT NULL,
  `total_despesa` decimal(12,2) DEFAULT NULL,
  `conta_receber_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fretes_empresa_id_foreign` (`empresa_id`),
  KEY `fretes_veiculo_id_foreign` (`veiculo_id`),
  KEY `fretes_cliente_id_foreign` (`cliente_id`),
  KEY `fretes_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `fretes_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `fretes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `fretes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `fretes_veiculo_id_foreign` FOREIGN KEY (`veiculo_id`) REFERENCES `veiculos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `despesa_fretes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `frete_id` bigint unsigned DEFAULT NULL,
  `tipo_despesa_id` bigint unsigned DEFAULT NULL,
  `fornecedor_id` bigint unsigned DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `observacao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `despesa_fretes_frete_id_foreign` (`frete_id`),
  KEY `despesa_fretes_tipo_despesa_id_foreign` (`tipo_despesa_id`),
  KEY `despesa_fretes_fornecedor_id_foreign` (`fornecedor_id`),
  CONSTRAINT `despesa_fretes_fornecedor_id_foreign` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedors` (`id`),
  CONSTRAINT `despesa_fretes_frete_id_foreign` FOREIGN KEY (`frete_id`) REFERENCES `fretes` (`id`),
  CONSTRAINT `despesa_fretes_tipo_despesa_id_foreign` FOREIGN KEY (`tipo_despesa_id`) REFERENCES `tipo_despesa_fretes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `manutencao_veiculos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `veiculo_id` bigint unsigned NOT NULL,
  `fornecedor_id` bigint unsigned NOT NULL,
  `numero_sequencial` int DEFAULT NULL,
  `observacao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `desconto` decimal(10,2) DEFAULT NULL,
  `acrescimo` decimal(10,2) DEFAULT NULL,
  `conta_pagar_id` tinyint(1) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `estado` enum('aguardando','em_manutencao','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `manutencao_veiculos_empresa_id_foreign` (`empresa_id`),
  KEY `manutencao_veiculos_veiculo_id_foreign` (`veiculo_id`),
  KEY `manutencao_veiculos_fornecedor_id_foreign` (`fornecedor_id`),
  CONSTRAINT `manutencao_veiculos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `manutencao_veiculos_fornecedor_id_foreign` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedors` (`id`),
  CONSTRAINT `manutencao_veiculos_veiculo_id_foreign` FOREIGN KEY (`veiculo_id`) REFERENCES `veiculos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `manutencao_veiculo_servicos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `manutencao_id` bigint unsigned NOT NULL,
  `servico_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(6,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `observacao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `manutencao_veiculo_servicos_manutencao_id_foreign` (`manutencao_id`),
  KEY `manutencao_veiculo_servicos_servico_id_foreign` (`servico_id`),
  CONSTRAINT `manutencao_veiculo_servicos_manutencao_id_foreign` FOREIGN KEY (`manutencao_id`) REFERENCES `manutencao_veiculos` (`id`),
  CONSTRAINT `manutencao_veiculo_servicos_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `manutencao_veiculo_produtos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `manutencao_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(6,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `observacao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `manutencao_veiculo_produtos_manutencao_id_foreign` (`manutencao_id`),
  KEY `manutencao_veiculo_produtos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `manutencao_veiculo_produtos_manutencao_id_foreign` FOREIGN KEY (`manutencao_id`) REFERENCES `manutencao_veiculos` (`id`),
  CONSTRAINT `manutencao_veiculo_produtos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `manutencao_veiculo_anexos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `manutencao_id` bigint unsigned NOT NULL,
  `arquivo` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `manutencao_veiculo_anexos_manutencao_id_foreign` (`manutencao_id`),
  CONSTRAINT `manutencao_veiculo_anexos_manutencao_id_foreign` FOREIGN KEY (`manutencao_id`) REFERENCES `manutencao_veiculos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `frete_anexos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `frete_id` bigint unsigned NOT NULL,
  `arquivo` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `frete_anexos_frete_id_foreign` (`frete_id`),
  CONSTRAINT `frete_anexos_frete_id_foreign` FOREIGN KEY (`frete_id`) REFERENCES `fretes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `email_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `host` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `porta` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cripitografia` enum('ssl','tls') COLLATE utf8mb4_unicode_ci NOT NULL,
  `smtp_auth` tinyint(1) NOT NULL,
  `smtp_debug` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_configs_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `email_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `escritorio_contabils` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cidade_id` bigint unsigned DEFAULT NULL,
  `razao_social` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_fantasia` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnpj` varchar(19) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ie` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rua` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bairro` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cep` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `crc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `envio_xml_automatico` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `escritorio_contabils_empresa_id_foreign` (`empresa_id`),
  KEY `escritorio_contabils_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `escritorio_contabils_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `escritorio_contabils_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `configuracao_agendamentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `token_whatsapp` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempo_descanso_entre_agendamento` int NOT NULL DEFAULT '0',
  `msg_wpp_manha` tinyint(1) NOT NULL DEFAULT '0',
  `msg_wpp_manha_horario` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `msg_wpp_alerta` tinyint(1) NOT NULL DEFAULT '0',
  `msg_wpp_alerta_minutos_antecedencia` int DEFAULT NULL,
  `mensagem_manha` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensagem_alerta` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `configuracao_agendamentos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `configuracao_agendamentos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lista_preco_usuarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lista_preco_id` bigint unsigned NOT NULL,
  `usuario_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lista_preco_usuarios_lista_preco_id_foreign` (`lista_preco_id`),
  KEY `lista_preco_usuarios_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `lista_preco_usuarios_lista_preco_id_foreign` FOREIGN KEY (`lista_preco_id`) REFERENCES `lista_precos` (`id`),
  CONSTRAINT `lista_preco_usuarios_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sped_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `codigo_conta_analitica` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_receita` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gerar_bloco_k` tinyint(1) NOT NULL DEFAULT '0',
  `layout_bloco_k` int NOT NULL DEFAULT '0',
  `codigo_obrigacao` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '000',
  `data_vencimento` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '10',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sped_configs_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `sped_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `speds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `data_refrencia` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saldo_credor` decimal(14,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `speds_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `speds_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `relacao_dados_fornecedors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cst_csosn_entrada` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_entrada` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_csosn_saida` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_saida` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `relacao_dados_fornecedors_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `relacao_dados_fornecedors_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `inicio` date NOT NULL,
  `fim` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  `referencia` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_sequencial` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventarios_empresa_id_foreign` (`empresa_id`),
  KEY `inventarios_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `inventarios_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `inventarios_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_inventarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inventario_id` bigint unsigned DEFAULT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `observacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_inventarios_inventario_id_foreign` (`inventario_id`),
  KEY `item_inventarios_produto_id_foreign` (`produto_id`),
  KEY `item_inventarios_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `item_inventarios_inventario_id_foreign` FOREIGN KEY (`inventario_id`) REFERENCES `inventarios` (`id`),
  CONSTRAINT `item_inventarios_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `item_inventarios_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `convenios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `nome` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `convenios_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `convenios_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `medicos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crm` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade_id` bigint unsigned DEFAULT NULL,
  `rua` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medicos_empresa_id_foreign` (`empresa_id`),
  KEY `medicos_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `medicos_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `medicos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `medicao_receita_os` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ordem_servico_id` bigint unsigned NOT NULL,
  `esferico_longe_od` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `esferico_longe_oe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `esferico_perto_od` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `esferico_perto_oe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cilindrico_longe_od` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cilindrico_longe_oe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cilindrico_perto_od` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cilindrico_perto_oe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eixo_longe_od` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eixo_longe_oe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eixo_perto_od` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eixo_perto_oe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `altura_longe_od` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `altura_longe_oe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `altura_perto_od` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `altura_perto_oe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dnp_longe_od` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dnp_longe_oe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dnp_perto_od` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dnp_perto_oe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medicao_receita_os_ordem_servico_id_foreign` (`ordem_servico_id`),
  CONSTRAINT `medicao_receita_os_ordem_servico_id_foreign` FOREIGN KEY (`ordem_servico_id`) REFERENCES `ordem_servicos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuario_emissaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint unsigned NOT NULL,
  `numero_serie_nfce` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_ultima_nfce` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_emissaos_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `usuario_emissaos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `otica_os` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ordem_servico_id` bigint unsigned NOT NULL,
  `convenio_id` int DEFAULT NULL,
  `medico_id` int DEFAULT NULL,
  `tipo_armacao_id` int DEFAULT NULL,
  `laboratorio_id` int DEFAULT NULL,
  `formato_armacao_id` int DEFAULT NULL,
  `validade` date DEFAULT NULL,
  `arquivo_receita` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacao_receita` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_lente` enum('Pronta','Surfaçada') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `material_lente` enum('Policarbonato','Resina','Trivex') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao_lente` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coloracao_lente` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armacao_propria` tinyint(1) NOT NULL,
  `armacao_segue` tinyint(1) NOT NULL,
  `armacao_aro` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armacao_ponte` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armacao_maior_diagonal` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armacao_altura_vertical` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armacao_distancia_pupilar` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armacao_altura_centro_longe_od` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armacao_altura_centro_longe_oe` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armacao_altura_centro_perto_od` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armacao_altura_centro_perto_oe` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tratamentos` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otica_os_ordem_servico_id_foreign` (`ordem_servico_id`),
  CONSTRAINT `otica_os_ordem_servico_id_foreign` FOREIGN KEY (`ordem_servico_id`) REFERENCES `ordem_servicos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tipo_armacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `nome` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipo_armacaos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `tipo_armacaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `laboratorios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnpj` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade_id` bigint unsigned DEFAULT NULL,
  `rua` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `laboratorios_empresa_id_foreign` (`empresa_id`),
  KEY `laboratorios_cidade_id_foreign` (`cidade_id`),
  CONSTRAINT `laboratorios_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`),
  CONSTRAINT `laboratorios_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tratamento_oticas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `nome` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tratamento_oticas_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `tratamento_oticas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `formato_armacao_oticas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `nome` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `imagem` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `formato_armacao_oticas_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `formato_armacao_oticas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `meta_resultados` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `funcionario_id` bigint unsigned NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `local_id` int DEFAULT NULL,
  `tabela` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meta_resultados_empresa_id_foreign` (`empresa_id`),
  KEY `meta_resultados_funcionario_id_foreign` (`funcionario_id`),
  CONSTRAINT `meta_resultados_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `meta_resultados_funcionario_id_foreign` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `percurso_cte_os` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cteos_id` bigint unsigned NOT NULL,
  `uf` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `percurso_cte_os_cteos_id_foreign` (`cteos_id`),
  CONSTRAINT `percurso_cte_os_cteos_id_foreign` FOREIGN KEY (`cteos_id`) REFERENCES `cte_os` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_dimensao_nves` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_nfe_id` bigint unsigned DEFAULT NULL,
  `valor_unitario_m2` decimal(12,2) NOT NULL,
  `largura` decimal(12,2) NOT NULL,
  `altura` decimal(12,2) NOT NULL,
  `quantidade` decimal(12,2) NOT NULL,
  `m2_total` decimal(12,2) NOT NULL,
  `sub_total` decimal(12,2) NOT NULL,
  `espessura` decimal(12,2) NOT NULL,
  `observacao` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_dimensao_nves_item_nfe_id_foreign` (`item_nfe_id`),
  CONSTRAINT `item_dimensao_nves_item_nfe_id_foreign` FOREIGN KEY (`item_nfe_id`) REFERENCES `item_nves` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produto_tributacao_locals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned NOT NULL,
  `local_id` bigint unsigned NOT NULL,
  `ncm` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `perc_icms` decimal(10,2) DEFAULT NULL,
  `perc_pis` decimal(10,2) DEFAULT NULL,
  `perc_cofins` decimal(10,2) DEFAULT NULL,
  `perc_ipi` decimal(10,2) DEFAULT NULL,
  `perc_red_bc` decimal(5,2) DEFAULT NULL,
  `cest` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origem` int DEFAULT NULL,
  `cst_csosn` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_pis` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_cofins` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_ipi` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_unitario` decimal(12,4) DEFAULT NULL,
  `cfop_estadual` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_outro_estado` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_tributacao_locals_produto_id_foreign` (`produto_id`),
  KEY `produto_tributacao_locals_local_id_foreign` (`local_id`),
  CONSTRAINT `produto_tributacao_locals_local_id_foreign` FOREIGN KEY (`local_id`) REFERENCES `localizacaos` (`id`),
  CONSTRAINT `produto_tributacao_locals_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `crm_anotacaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `fornecedor_id` bigint unsigned DEFAULT NULL,
  `funcionario_id` int DEFAULT NULL,
  `registro_id` int DEFAULT NULL,
  `tipo_registro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('positivo','bom','negativo') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conclusao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assunto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alerta` tinyint(1) NOT NULL,
  `data_retorno` date DEFAULT NULL,
  `data_entrega` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_anotacaos_empresa_id_foreign` (`empresa_id`),
  KEY `crm_anotacaos_cliente_id_foreign` (`cliente_id`),
  KEY `crm_anotacaos_fornecedor_id_foreign` (`fornecedor_id`),
  CONSTRAINT `crm_anotacaos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `crm_anotacaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `crm_anotacaos_fornecedor_id_foreign` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedors` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `crm_anotacao_notas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crm_anotacao_id` bigint unsigned NOT NULL,
  `nota` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_anotacao_notas_crm_anotacao_id_foreign` (`crm_anotacao_id`),
  CONSTRAINT `crm_anotacao_notas_crm_anotacao_id_foreign` FOREIGN KEY (`crm_anotacao_id`) REFERENCES `crm_anotacaos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_producaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned NOT NULL,
  `quantidade` decimal(12,3) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `item_id` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_producaos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `item_producaos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ordem_producaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `funcionario_id` bigint unsigned DEFAULT NULL,
  `usuario_id` bigint unsigned NOT NULL,
  `observacao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('novo','producao','expedicao','entregue') COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_prevista_entrega` date DEFAULT NULL,
  `codigo_sequencial` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ordem_producaos_empresa_id_foreign` (`empresa_id`),
  KEY `ordem_producaos_funcionario_id_foreign` (`funcionario_id`),
  KEY `ordem_producaos_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `ordem_producaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `ordem_producaos_funcionario_id_foreign` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`),
  CONSTRAINT `ordem_producaos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_ordem_producaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ordem_producao_id` bigint unsigned NOT NULL,
  `item_producao_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `quantidade` decimal(12,3) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `observacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_ordem_producaos_ordem_producao_id_foreign` (`ordem_producao_id`),
  KEY `item_ordem_producaos_item_producao_id_foreign` (`item_producao_id`),
  KEY `item_ordem_producaos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `item_ordem_producaos_item_producao_id_foreign` FOREIGN KEY (`item_producao_id`) REFERENCES `item_producaos` (`id`),
  CONSTRAINT `item_ordem_producaos_ordem_producao_id_foreign` FOREIGN KEY (`ordem_producao_id`) REFERENCES `ordem_producaos` (`id`),
  CONSTRAINT `item_ordem_producaos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_pedido_servicos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `servico_id` bigint unsigned NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('novo','pendente','preparando','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'novo',
  `quantidade` decimal(8,3) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_pedido_servicos_pedido_id_foreign` (`pedido_id`),
  KEY `item_pedido_servicos_servico_id_foreign` (`servico_id`),
  CONSTRAINT `item_pedido_servicos_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  CONSTRAINT `item_pedido_servicos_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `financeiro_boletos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `valor_recebido` decimal(10,2) NOT NULL,
  `juros` decimal(10,2) NOT NULL,
  `multa` decimal(10,2) NOT NULL,
  `vencimento` date NOT NULL,
  `data_recebimento` date DEFAULT NULL,
  `pdf_boleto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `plano_id` int DEFAULT NULL,
  `data_liquidacao` date DEFAULT NULL,
  `_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financeiro_boletos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `financeiro_boletos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `log_boletos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo` enum('confirmacao','geracao') COLLATE utf8mb4_unicode_ci NOT NULL,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_boletos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `log_boletos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contrato_empresas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `assinado` tinyint(1) NOT NULL DEFAULT '0',
  `data_assinatura` timestamp NULL DEFAULT NULL,
  `cpf_cnpj` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contrato_empresas_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `contrato_empresas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contrato_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `texto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `limite_dias_assinar` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tributacao_clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned NOT NULL,
  `perc_icms` decimal(10,2) DEFAULT NULL,
  `perc_pis` decimal(10,2) DEFAULT NULL,
  `perc_cofins` decimal(10,2) DEFAULT NULL,
  `perc_ipi` decimal(10,2) DEFAULT NULL,
  `cst_csosn` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_pis` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_cofins` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_ipi` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_estadual` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_outro_estado` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `perc_red_bc` decimal(5,2) DEFAULT NULL,
  `cest` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ncm` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_beneficio_fiscal` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tributacao_clientes_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `tributacao_clientes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `impressora_pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `descricao` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `impressora_pedidos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `impressora_pedidos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `impressora_pedido_produtos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `impressora_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `impressora_pedido_produtos_impressora_id_foreign` (`impressora_id`),
  KEY `impressora_pedido_produtos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `impressora_pedido_produtos_impressora_id_foreign` FOREIGN KEY (`impressora_id`) REFERENCES `impressora_pedidos` (`id`),
  CONSTRAINT `impressora_pedido_produtos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1068 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `etiqueta_configuracaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `margem_topo` decimal(7,2) NOT NULL,
  `margem_lateral` decimal(7,2) NOT NULL,
  `distancia_entre_etiquetas` decimal(7,2) NOT NULL,
  `distancia_entre_linhas` decimal(7,2) NOT NULL,
  `largura_imagem` decimal(7,2) NOT NULL,
  `altura_imagem` decimal(7,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `etiqueta_configuracaos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `etiqueta_configuracaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_adicionals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `minimo_escolha` int NOT NULL DEFAULT '0',
  `maximo_escolha` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_adicionals_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `categoria_adicionals_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `registro_empresas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ifood_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `clientId` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clientSecret` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grantType` enum('authorization_code','client_credentials','refresh_token') COLLATE utf8mb4_unicode_ci NOT NULL,
  `userCode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authorizationCode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authorizationCodeVerifier` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verificationUrlComplete` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accessToken` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `refreshToken` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `merchantId` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catalogId` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ifood_configs_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `ifood_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `natureza_operacao_supers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cst_csosn` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_pis` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_cofins` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_ipi` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_estadual` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_outro_estado` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_entrada_estadual` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_entrada_outro_estado` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `perc_icms` decimal(5,2) DEFAULT NULL,
  `perc_pis` decimal(5,2) DEFAULT NULL,
  `perc_cofins` decimal(5,2) DEFAULT NULL,
  `perc_ipi` decimal(5,2) DEFAULT NULL,
  `padrao` tinyint(1) NOT NULL DEFAULT '0',
  `sobrescrever_cfop` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `padrao_tributacao_produto_supers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `descricao` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perc_icms` decimal(10,2) NOT NULL DEFAULT '0.00',
  `perc_pis` decimal(10,2) NOT NULL DEFAULT '0.00',
  `perc_cofins` decimal(10,2) NOT NULL DEFAULT '0.00',
  `perc_ipi` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cst_csosn` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_pis` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_cofins` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst_ipi` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_estadual` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cfop_outro_estado` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cfop_entrada_estadual` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_entrada_outro_estado` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cEnq` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `perc_red_bc` decimal(5,2) DEFAULT NULL,
  `pST` decimal(5,2) DEFAULT NULL,
  `cest` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ncm` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_beneficio_fiscal` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `padrao` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `modBCST` int DEFAULT NULL,
  `pMVAST` decimal(5,2) DEFAULT NULL,
  `pICMSST` decimal(5,2) DEFAULT NULL,
  `redBCST` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_produto_ifoods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `ifood_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_produto_ifoods_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `categoria_produto_ifoods_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produto_ibpts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned NOT NULL,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uf` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nacional` decimal(5,2) NOT NULL,
  `estadual` decimal(5,2) NOT NULL,
  `importado` decimal(5,2) NOT NULL,
  `municipal` decimal(5,2) NOT NULL,
  `vigencia_inicio` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vigencia_fim` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chave` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `versao` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fonte` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_ibpts_produto_id_foreign` (`produto_id`),
  CONSTRAINT `produto_ibpts_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `vendi_zap_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `auth_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_secret` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendi_zap_configs_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `vendi_zap_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_vendi_zaps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_vendi_zaps_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `categoria_vendi_zaps_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pedido_vendi_zaps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `numero_pedido` int NOT NULL,
  `data` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rua` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uf` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entrega` tinyint(1) NOT NULL,
  `taxa_entrega` decimal(12,2) DEFAULT NULL,
  `taxa_retirada` decimal(12,2) DEFAULT NULL,
  `_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_link_rastreio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_pagamento` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nfe_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_vendi_zaps_empresa_id_foreign` (`empresa_id`),
  KEY `pedido_vendi_zaps_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `pedido_vendi_zaps_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `pedido_vendi_zaps_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_pedido_vendi_zaps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `vendizap_produto_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalhes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `unidade` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` decimal(12,2) NOT NULL,
  `valor_promociconal` decimal(12,2) DEFAULT NULL,
  `quantidade` decimal(12,2) NOT NULL,
  `sub_total` decimal(12,2) NOT NULL,
  `valor_adicionais` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_pedido_vendi_zaps_pedido_id_foreign` (`pedido_id`),
  KEY `item_pedido_vendi_zaps_produto_id_foreign` (`produto_id`),
  CONSTRAINT `item_pedido_vendi_zaps_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedido_vendi_zaps` (`id`),
  CONSTRAINT `item_pedido_vendi_zaps_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mesas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ocupada` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mesas_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `mesas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `carrinho_cardapios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `session_cart_cardapio` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `observacao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('pendente','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cliente_nome` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrinho_cardapios_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `carrinho_cardapios_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_carrinho_cardapios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `carrinho_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `tamanho_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(8,3) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,3) NOT NULL,
  `observacao` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_carrinho_cardapios_carrinho_id_foreign` (`carrinho_id`),
  KEY `item_carrinho_cardapios_produto_id_foreign` (`produto_id`),
  KEY `item_carrinho_cardapios_tamanho_id_foreign` (`tamanho_id`),
  CONSTRAINT `item_carrinho_cardapios_carrinho_id_foreign` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinho_cardapios` (`id`),
  CONSTRAINT `item_carrinho_cardapios_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `item_carrinho_cardapios_tamanho_id_foreign` FOREIGN KEY (`tamanho_id`) REFERENCES `tamanho_pizzas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_carrinho_adicional_cardapios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_carrinho_id` bigint unsigned NOT NULL,
  `adicional_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_carrinho_adicional_cardapios_item_carrinho_id_foreign` (`item_carrinho_id`),
  KEY `item_carrinho_adicional_cardapios_adicional_id_foreign` (`adicional_id`),
  CONSTRAINT `item_carrinho_adicional_cardapios_adicional_id_foreign` FOREIGN KEY (`adicional_id`) REFERENCES `adicionals` (`id`),
  CONSTRAINT `item_carrinho_adicional_cardapios_item_carrinho_id_foreign` FOREIGN KEY (`item_carrinho_id`) REFERENCES `item_carrinho_cardapios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_pizza_carrinho_cardapios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_carrinho_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_pizza_carrinho_cardapios_item_carrinho_id_foreign` (`item_carrinho_id`),
  KEY `item_pizza_carrinho_cardapios_produto_id_foreign` (`produto_id`),
  CONSTRAINT `item_pizza_carrinho_cardapios_item_carrinho_id_foreign` FOREIGN KEY (`item_carrinho_id`) REFERENCES `item_carrinho_cardapios` (`id`),
  CONSTRAINT `item_pizza_carrinho_cardapios_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produto_ifoods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `ifood_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ifood_id_aux` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria_produto_ifood_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `imagem` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serving` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estoque` decimal(10,2) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `sellingOption_minimum` int DEFAULT NULL,
  `sellingOption_incremental` int DEFAULT NULL,
  `sellingOption_averageUnit` int DEFAULT NULL,
  `sellingOption_availableUnits` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_ifoods_empresa_id_foreign` (`empresa_id`),
  KEY `produto_ifoods_produto_id_foreign` (`produto_id`),
  CONSTRAINT `produto_ifoods_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `produto_ifoods_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mensagem_agendamento_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mensagem` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mensagem_agendamento_logs_empresa_id_foreign` (`empresa_id`),
  KEY `mensagem_agendamento_logs_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `mensagem_agendamento_logs_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `mensagem_agendamento_logs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_contas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('receber','pagar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_contas_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `categoria_contas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `retirada_estoques` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motivo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `local_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `retirada_estoques_produto_id_foreign` (`produto_id`),
  KEY `retirada_estoques_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `retirada_estoques_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `retirada_estoques_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_adicional_nfces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_nfce_id` bigint unsigned NOT NULL,
  `adicional_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_adicional_nfces_item_nfce_id_foreign` (`item_nfce_id`),
  KEY `item_adicional_nfces_adicional_id_foreign` (`adicional_id`),
  CONSTRAINT `item_adicional_nfces_adicional_id_foreign` FOREIGN KEY (`adicional_id`) REFERENCES `adicionals` (`id`),
  CONSTRAINT `item_adicional_nfces_item_nfce_id_foreign` FOREIGN KEY (`item_nfce_id`) REFERENCES `item_nfces` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_pizza_nfces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_nfce_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_pizza_nfces_item_nfce_id_foreign` (`item_nfce_id`),
  KEY `item_pizza_nfces_produto_id_foreign` (`produto_id`),
  CONSTRAINT `item_pizza_nfces_item_nfce_id_foreign` FOREIGN KEY (`item_nfce_id`) REFERENCES `item_nfces` (`id`),
  CONSTRAINT `item_pizza_nfces_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `planejamento_custos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `numero_sequencial` int DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `observacao` text COLLATE utf8mb4_unicode_ci,
  `data_prevista_entrega` date DEFAULT NULL,
  `data_entrega` date DEFAULT NULL,
  `arquivo` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `estado` enum('novo','cotacao','proposta','producao','finalizado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'novo',
  `compra_id` int DEFAULT NULL,
  `venda_id` int DEFAULT NULL,
  `local_id` int DEFAULT NULL,
  `total_custo` decimal(14,2) NOT NULL,
  `total_final` decimal(14,2) NOT NULL,
  `desconto` decimal(14,2) NOT NULL,
  `frete` decimal(14,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `planejamento_custos_empresa_id_foreign` (`empresa_id`),
  KEY `planejamento_custos_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `planejamento_custos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `planejamento_custos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produto_planejamento_custos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `planejamento_id` bigint unsigned DEFAULT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(12,4) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `largura` decimal(10,2) DEFAULT NULL,
  `espessura` decimal(10,2) DEFAULT NULL,
  `comprimento` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_planejamento_custos_planejamento_id_foreign` (`planejamento_id`),
  KEY `produto_planejamento_custos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `produto_planejamento_custos_planejamento_id_foreign` FOREIGN KEY (`planejamento_id`) REFERENCES `planejamento_custos` (`id`),
  CONSTRAINT `produto_planejamento_custos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `servico_planejamento_custos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `planejamento_id` bigint unsigned DEFAULT NULL,
  `servico_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(12,4) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `terceiro` tinyint(1) NOT NULL DEFAULT '0',
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `servico_planejamento_custos_planejamento_id_foreign` (`planejamento_id`),
  KEY `servico_planejamento_custos_servico_id_foreign` (`servico_id`),
  CONSTRAINT `servico_planejamento_custos_planejamento_id_foreign` FOREIGN KEY (`planejamento_id`) REFERENCES `planejamento_custos` (`id`),
  CONSTRAINT `servico_planejamento_custos_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `planejamento_custo_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `planejamento_id` bigint unsigned DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `estado_anterior` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_alterado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `planejamento_custo_logs_planejamento_id_foreign` (`planejamento_id`),
  CONSTRAINT `planejamento_custo_logs_planejamento_id_foreign` FOREIGN KEY (`planejamento_id`) REFERENCES `planejamento_custos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `custo_adm_planejamento_custos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `planejamento_id` bigint unsigned DEFAULT NULL,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` decimal(12,4) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custo_adm_planejamento_custos_planejamento_id_foreign` (`planejamento_id`),
  CONSTRAINT `custo_adm_planejamento_custos_planejamento_id_foreign` FOREIGN KEY (`planejamento_id`) REFERENCES `planejamento_custos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_proposta_planejamento_custos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `planejamento_id` bigint unsigned NOT NULL,
  `descricao` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` decimal(12,4) NOT NULL,
  `valor_unitario_custo` decimal(10,2) NOT NULL,
  `valor_unitario_final` decimal(10,2) NOT NULL,
  `sub_total_custo` decimal(10,2) NOT NULL,
  `sub_total_final` decimal(10,2) NOT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `servico_id` int DEFAULT NULL,
  `produto_id` int DEFAULT NULL,
  `terceiro` tinyint(1) NOT NULL DEFAULT '0',
  `largura` decimal(10,2) DEFAULT NULL,
  `espessura` decimal(10,2) DEFAULT NULL,
  `comprimento` decimal(10,2) DEFAULT NULL,
  `peso_especifico` decimal(10,2) DEFAULT NULL,
  `calculo` decimal(14,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_proposta_planejamento_custos_planejamento_id_foreign` (`planejamento_id`),
  CONSTRAINT `item_proposta_planejamento_custos_planejamento_id_foreign` FOREIGN KEY (`planejamento_id`) REFERENCES `planejamento_custos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fatura_clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned NOT NULL,
  `tipo_pagamento` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dias_vencimento` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fatura_clientes_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `fatura_clientes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `projeto_custos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `numero_sequencial` int DEFAULT NULL,
  `numero_sequencial_ano` int DEFAULT NULL,
  `_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `observacao` text COLLATE utf8mb4_unicode_ci,
  `data_prevista_entrega` date DEFAULT NULL,
  `data_entrega` date DEFAULT NULL,
  `arquivo` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `estado` enum('novo','cotacao','proposta','producao','finalizado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'novo',
  `compra_id` int DEFAULT NULL,
  `venda_id` int DEFAULT NULL,
  `local_id` int DEFAULT NULL,
  `total_custo` decimal(14,2) NOT NULL,
  `total_final` decimal(14,2) NOT NULL,
  `desconto` decimal(14,2) NOT NULL,
  `frete` decimal(14,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projeto_custos_empresa_id_foreign` (`empresa_id`),
  KEY `projeto_custos_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `projeto_custos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `projeto_custos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `promocao_produtos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned NOT NULL,
  `valor` decimal(12,4) NOT NULL,
  `valor_original` decimal(12,4) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promocao_produtos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `promocao_produtos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gestao_custo_producaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_sequencial` int DEFAULT NULL,
  `empresa_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `data_finalizacao` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `total_custo_produtos` decimal(14,2) NOT NULL,
  `total_custo_servicos` decimal(14,2) NOT NULL,
  `total_custo_outros` decimal(14,2) NOT NULL,
  `desconto` decimal(14,2) DEFAULT NULL,
  `total_final` decimal(14,2) NOT NULL,
  `frete` decimal(14,2) DEFAULT NULL,
  `quantidade` decimal(12,4) NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gestao_custo_producaos_empresa_id_foreign` (`empresa_id`),
  KEY `gestao_custo_producaos_produto_id_foreign` (`produto_id`),
  KEY `gestao_custo_producaos_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `gestao_custo_producaos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `gestao_custo_producaos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `gestao_custo_producaos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gestao_custo_producao_produtos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gestao_custo_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `quantidade` decimal(12,4) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gestao_custo_producao_produtos_gestao_custo_id_foreign` (`gestao_custo_id`),
  KEY `gestao_custo_producao_produtos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `gestao_custo_producao_produtos_gestao_custo_id_foreign` FOREIGN KEY (`gestao_custo_id`) REFERENCES `gestao_custo_producaos` (`id`),
  CONSTRAINT `gestao_custo_producao_produtos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gestao_custo_producao_servicos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gestao_custo_id` bigint unsigned NOT NULL,
  `servico_id` bigint unsigned NOT NULL,
  `quantidade` decimal(12,4) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gestao_custo_producao_servicos_gestao_custo_id_foreign` (`gestao_custo_id`),
  KEY `gestao_custo_producao_servicos_servico_id_foreign` (`servico_id`),
  CONSTRAINT `gestao_custo_producao_servicos_gestao_custo_id_foreign` FOREIGN KEY (`gestao_custo_id`) REFERENCES `gestao_custo_producaos` (`id`),
  CONSTRAINT `gestao_custo_producao_servicos_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gestao_custo_producao_outro_custos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gestao_custo_id` bigint unsigned NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` decimal(12,4) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gestao_custo_producao_outro_custos_gestao_custo_id_foreign` (`gestao_custo_id`),
  CONSTRAINT `gestao_custo_producao_outro_custos_gestao_custo_id_foreign` FOREIGN KEY (`gestao_custo_id`) REFERENCES `gestao_custo_producaos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mensagem_padrao_crms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensagem` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `tipo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enviar_whatsapp` tinyint(1) NOT NULL,
  `enviar_email` tinyint(1) NOT NULL,
  `horario_envio` time DEFAULT NULL,
  `dias_apos_venda` int DEFAULT NULL,
  `dias_ultima_venda` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mensagem_padrao_crms_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `mensagem_padrao_crms_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `estoque_atual_produtos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(14,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estoque_atual_produtos_produto_id_foreign` (`produto_id`),
  CONSTRAINT `estoque_atual_produtos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fila_envio_crons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `mensagem` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `enviado_em` timestamp NULL DEFAULT NULL,
  `agendar_para` date DEFAULT NULL,
  `status` enum('pendente','enviado','erro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `erro` text COLLATE utf8mb4_unicode_ci,
  `enviar_whatsapp` tinyint(1) NOT NULL,
  `enviar_email` tinyint(1) NOT NULL,
  `whatsapp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fila_envio_crons_empresa_id_foreign` (`empresa_id`),
  KEY `fila_envio_crons_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `fila_envio_crons_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `fila_envio_crons_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `componente_mdves` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mdfe_id` bigint unsigned NOT NULL,
  `tipo` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `descricao` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `componente_mdves_mdfe_id_foreign` (`mdfe_id`),
  CONSTRAINT `componente_mdves_mdfe_id_foreign` FOREIGN KEY (`mdfe_id`) REFERENCES `mdves` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `parcelamento_mdves` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mdfe_id` bigint unsigned NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `vencimento` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parcelamento_mdves_mdfe_id_foreign` (`mdfe_id`),
  CONSTRAINT `parcelamento_mdves_mdfe_id_foreign` FOREIGN KEY (`mdfe_id`) REFERENCES `mdves` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `informacao_bancaria_mdves` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mdfe_id` bigint unsigned NOT NULL,
  `codigo_banco` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_agencia` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnpj_ipef` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `informacao_bancaria_mdves_mdfe_id_foreign` (`mdfe_id`),
  CONSTRAINT `informacao_bancaria_mdves_mdfe_id_foreign` FOREIGN KEY (`mdfe_id`) REFERENCES `mdves` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `garantias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `usuario_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `nfe_id` int DEFAULT NULL,
  `nfce_id` int DEFAULT NULL,
  `data_venda` date DEFAULT NULL,
  `data_solicitacao` date DEFAULT NULL,
  `prazo_garantia` int NOT NULL DEFAULT '0',
  `descricao_problema` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_reparo` decimal(10,2) DEFAULT NULL,
  `status` enum('registrada','em análise','aprovada','recusada','concluída','expirada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'registrada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `garantias_empresa_id_foreign` (`empresa_id`),
  KEY `garantias_usuario_id_foreign` (`usuario_id`),
  KEY `garantias_produto_id_foreign` (`produto_id`),
  KEY `garantias_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `garantias_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `garantias_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `garantias_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `garantias_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pdv_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `usuario_id` bigint unsigned NOT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `acao` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_desconto` decimal(10,2) DEFAULT NULL,
  `valor_acrescimo` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pdv_logs_empresa_id_foreign` (`empresa_id`),
  KEY `pdv_logs_usuario_id_foreign` (`usuario_id`),
  KEY `pdv_logs_produto_id_foreign` (`produto_id`),
  CONSTRAINT `pdv_logs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `pdv_logs_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `pdv_logs_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fatura_ordem_servicos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ordem_servico_id` bigint unsigned NOT NULL,
  `tipo_pagamento` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_vencimento` date NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fatura_ordem_servicos_ordem_servico_id_foreign` (`ordem_servico_id`),
  CONSTRAINT `fatura_ordem_servicos_ordem_servico_id_foreign` FOREIGN KEY (`ordem_servico_id`) REFERENCES `ordem_servicos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `item_inventario_impressaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inventario_id` bigint unsigned DEFAULT NULL,
  `produto_id` bigint unsigned DEFAULT NULL,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `quantidade` decimal(10,2) DEFAULT NULL,
  `observacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_inventario_impressaos_inventario_id_foreign` (`inventario_id`),
  KEY `item_inventario_impressaos_produto_id_foreign` (`produto_id`),
  KEY `item_inventario_impressaos_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `item_inventario_impressaos_inventario_id_foreign` FOREIGN KEY (`inventario_id`) REFERENCES `inventarios` (`id`),
  CONSTRAINT `item_inventario_impressaos_produto_id_foreign` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  CONSTRAINT `item_inventario_impressaos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1297 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

