<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\Empresa;
use App\Models\PlanoConta;

class PlanoContaUtil {

	public function criaPlanoDeContas($empresa_id){
		$empresa = Empresa::findOrFail($empresa_id);
		$data = $this->arrayPlanoDeContas();

		PlanoConta::where('empresa_id', $empresa_id)->delete();
		foreach($data as $key => $p){
			if(is_string($key)){
				$plano = $this->addPlano($key, null, $empresa_id);
			}
			$this->percorre($p, $plano->id, $empresa_id);
		}

	}

	private function percorre($array, $plano_conta_id, $empresa_id){
		// echo $plano_conta_id;
		foreach($array as $key => $a){
			$plano = $this->addPlano($key, $plano_conta_id, $empresa_id);
			$id1 = $plano->id;

			foreach($a as $ch => $r){

				if($plano){
					$plano = $this->addPlano($ch, $id1, $empresa_id);
					if($plano){

						$id2 = $plano->id;
						try{
							foreach($r as $ch2 => $a2){
								$plano = $this->addPlano($a2, $id2, $empresa_id);
							}
						}catch(\Exception $e){
							$plano = $this->addPlano($r, $id2, $empresa_id);
						}
					}
				}
			}
		}
	}

	private function addPlano($descricao, $plano_id, $empresa_id){
		if(strlen($descricao) > 5){
			return PlanoConta::create([
				'empresa_id' => $empresa_id,
				'plano_conta_id' => $plano_id,
				'descricao' => $descricao,
			]);
		}
		return null;
	}

	private function arrayPlanoDeContas(){
		$data = [
			'1 - Ativo' => [
				'1.1 - Ativo Circulante' => [
					'1.1.1 - Caixa' => [
						'1.1.1.01 - Caixa Geral'
					],
					'1.1.2 - Banco com Movimento' => [
						'1.1.2.01 - Banco do Brasil'
					],
					'1.1.3 - Contas a receber' => [
						'1.1.3.01 - Clientes',
						'1.1.3.02 - Outras Contas a Receber',
						'1.1.3.03 - (-) Duplicatas Descontadas',
					],
					'1.1.4 - Estoques' => [
						'1.1.4.01 - Mercadorias',
						'1.1.4.02 - Produtos Acabados',
						'1.1.4.03 - Insumos',
						'1.1.4.04 - Outrtos',
					],
				],
				'1.2 - Não Circulante' => [
					'1.2.1 - Contas a Receber' => [
						'1.2.1.01 - Clientes', 
						'1.2.1.02 - Outras Contas', 
					],
					'1.2.2 - Investimentos' => [
						'1.2.2.01 - Participações Societárias'
					],
					'1.2.3 - Imobilizado' => [
						'1.2.3.01 - Terrenos',
						'1.2.3.02 - Construções e Benfeitorias',
						'1.2.3.03 - Maquinas e Ferramentas',
						'1.2.3.04 - Veículos',
						'1.2.3.04 - Móveis',
						'1.2.3.98 - (-) Depreciação Acumulada',
						'1.2.3.99 - (-) Amortização Acumulada',
					],
					'1.2.4 - Intangível' => [
						'1.2.4.01 - Marcas',
						'1.2.4.02 - Softwares',
						'1.2.4.99 - (-) Amortização Acumulada',
					]
				]
			],
			'2 - Passivo' => [
				'2.1 - Circulante' => [
					'2.1.1 - Impostos e Contribuições a Recolher' => [
						'2.1.1.01 - Simples a Recolher',
						'2.1.1.02 - INSS',
						'2.1.1.03 - FGTS',
					],
					'2.1.2 - Contas a Pagar' => [
						'2.1.2.01 - Fornecedores',
						'2.1.2.02 - Outras Contas'
					],
					'2.1.3 - Empréstimos Bancários'
				],
				'2.2 - Não Circulante' => [
					'2.2.1 - Empréstimos Bancários' => []
				],
				'2.3 - Patrimônio Líquido' => [
					'2.3.1 - Capital Social' => [
						'2.3.1.01 - Capital Social Subscrito',
						'2.3.1.02 - Capital Social a Realizar',
					],
					'2.3.2 - Reservas' => [
						'2.3.2.01 - Reservas de Capital',
						'2.3.2.02 - Reservas de Lucros',
					],
					'2.3.3 - Prejuízos Acumulados' => []
				]
			],
			'3 - Custos e despesas' => [
				'3.1 - Custos dos Produtos Vendidos' => [
					'3.1.1 - Custos dos Materiais' => [
						'3.1.1.01 - Custos dos Materiais Aplicados'
					],
					'3.1.2 - Custos da Mão de Obra' => [
						'3.1.2.01 - Salários'
					],
				],
				'3.2 - Custos das Mercadorias Vendidas' => [
					'3.2.1 - Custos das Mercadorias' => [
						'3.2.1.01 - Custos das Mercadorias Vendidas'
					]
				],
				'3.3 - Custo dos Serviços Prestados' => [
					'3.3.1 - Custo dos Serviços' => [
						'3.3.1.01 - Materiais Aplicados',
						'3.3.1.02 - Mão de Obra',
						'3.3.1.03 - Encargos Sociais',
					]
				],
				'3.4 - Despesas Operacionais' => [
					'3.4.1 - Despesas Gerais' => [
						'3.4.1.01 - Mão de Obra',
						'3.4.1.02 - Encargos Sociais',
					]
				],
				'3.5 - Perca de Capital' => [
					'3.5.1 - Baixa de Bens do Ativo não Circulante' => [],
					'3.5.2 - Custos de Alienação de Investimentos' => [],
					'3.5.3 - Custos de Alienação do Imobilizado' => [],
				]
			],
			'4 - Receitas' => [
				'4.1 - Receita Líquida' => [
					'4.1.1 - Receita Bruta de Vendas' => [
						'4.1.1.01 - De Mercadorias',
						'4.1.1.02 - De Produtos',
						'4.1.1.03 - De Serviços Prestados',
					],
					'4.1.2 - Deduções de Receita Bruta' => [
						'4.1.2.01 - Devoluções'
					]
				],
				'4.2 - Outras Receitas Operacionais' => [
					'4.2.1 - Vendas de Ativos Não Circulantes' => [
						'4.2.1.01 - Receitas de Alienação de Investimentos',
						'4.2.1.02 - Receitas de Alienação d Imobilizado',
					]
				]
			],
		];

		return $data;
	}
}