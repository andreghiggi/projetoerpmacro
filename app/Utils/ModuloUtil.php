<?php

namespace App\Utils;

use Illuminate\Support\Str;

class ModuloUtil
{
	public function getModulos(){
		return [
			'Produtos', 'Pessoas', 'Usuários', 'Compras', 'PDV', 'Vendas', 'NFCe', 'CTe', 'MDFe', 'Financeiro', 'Veiculos',
			'Serviços', 'Atendimento', 'Cardapio', 'Agendamentos', 'Delivery', 'Ecommerce', 'NFSe', 'Mercado Livre',
			'Nuvem Shop','Conecta Venda', 'Pré venda', 'Reservas', 'Localizações', 'Woocommerce', 'Controle de Fretes', 'Sped', 'Ordem de Produção',
			'CRM'
		];
	}

}
