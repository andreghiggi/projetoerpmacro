<?php

namespace App\Utils;
use App\Models\FilaEnvioCron;
use App\Models\MensagemPadraoCrm;
use App\Models\ConfigGeral;

class FilaEnvioUtil
{

	public function adicionaVendaFila($venda)
	{
		if(!$venda->cliente){
			return;
		}
		$cliente = $venda->cliente;
		$data = MensagemPadraoCrm::where('status', 1)
		->where('tipo', 'pos_venda')
		->where('empresa_id', $cliente->empresa_id)->get();

		FilaEnvioCron::where('status', 'pendente')
		->where('cliente_id', $cliente->id)
		->where('tipo', 'reativacao')
		->delete();

		foreach($data as $item){
			if($item->enviar_whatsapp && !$cliente->telefone){
				return;
			}

			if($item->enviar_email && !$cliente->email){
				return;
			}

			$dataAgendamento = date('Y-m-d', strtotime(date('Y-m-d') . " +$item->dias_apos_venda days"));
			FilaEnvioCron::create([
				'empresa_id' => $cliente->empresa_id,
				'mensagem' => $this->__replaceText($item, $cliente, $venda),
				'erro' => '',
				'enviar_whatsapp' => $item->enviar_whatsapp,
				'enviar_email' => $item->enviar_email,
				'whatsapp' => $cliente->telefone,
				'cliente_id' => $cliente->id,
				'tipo' => $item->tipo,
				'email' => $cliente->email,
				'agendar_para' => $dataAgendamento
			]);
		}
	}

	private function __replaceText($item, $cliente, $venda){
		$texto = str_replace("[nome_cliente]", $cliente->razao_social, $item->mensagem);
		$texto = str_replace("[data_venda]", __data_pt($venda->created_at, 0), $texto);
		$texto = str_replace("[valor_total]", __moeda($venda->total), $texto);
		$texto = str_replace("[data_agendamento]", __data_pt($venda->created_at, 0), $texto);

		return $texto;
	}

	public function adicionaAgendamentoFila($agendamento)
	{
		if(!$agendamento->cliente){
			return;
		}
		$cliente = $agendamento->cliente;
		$data = MensagemPadraoCrm::where('status', 1)
		->where('tipo', 'pos_agendamento')
		->where('empresa_id', $cliente->empresa_id)->get();

		FilaEnvioCron::where('status', 'pendente')
		->where('cliente_id', $cliente->id)
		->where('tipo', 'reativacao')
		->delete();

		foreach($data as $item){
			if($item->enviar_whatsapp && !$cliente->telefone){
				return;
			}

			if($item->enviar_email && !$cliente->email){
				return;
			}

			$dataAgendamento = date('Y-m-d', strtotime(date('Y-m-d') . " +$item->dias_apos_agendamento days"));
			FilaEnvioCron::create([
				'empresa_id' => $cliente->empresa_id,
				'mensagem' => $this->__replaceText($item, $cliente, $agendamento),
				'erro' => '',
				'enviar_whatsapp' => $item->enviar_whatsapp,
				'enviar_email' => $item->enviar_email,
				'whatsapp' => $cliente->telefone,
				'cliente_id' => $cliente->id,
				'tipo' => $item->tipo,
				'email' => $cliente->email,
				'agendar_para' => $dataAgendamento
			]);
		}
	}

}