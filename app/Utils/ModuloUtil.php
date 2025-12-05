<?php

namespace App\Utils;

use Illuminate\Support\Str;

class ModuloUtil
{
	public function getModulos(){
		$data = [
			'Produtos', 'Pessoas', 'Usuários', 'Compras', 'PDV', 'Vendas', 'NFCe', 'CTe', 'MDFe', 'Financeiro', 'Veiculos', 
			'Serviços', 'Atendimento', 'Cardapio', 'Agendamentos', 'Delivery', 'Ecommerce', 'NFSe', 'Mercado Livre', 
			'Nuvem Shop', 'Pré venda', 'Reservas', 'Localizações', 'Woocommerce', 'Controle de Fretes', 'Sped', 'Ordem de Produção',
			'CRM'
		];
		if(env("VENDIZAP") == 1){
			$data[] = "VendiZap";
		}
		if(env("IFOOD") == 1){
			$data[] = "IFood";
		}
		if(env("PLANEJAMENTO") == 1){
			$data[] = "Planejamento de Custos";
		}
		if(env("GESTAOPRODUCAO") == 1){
			$data[] = "Gestão de Produção";
		}
		return $data;
	}

}