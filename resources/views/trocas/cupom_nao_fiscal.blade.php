<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		body {
			font-family: Arial, sans-serif;
		}
		
		body{
			width: 330px;
			/*background: #000;*/
			margin-left: -45px;
			margin-top: -30px;
		}
		.mt-20{
			margin-top: -20px;
		}
		.mt-10{
			margin-top: -10px;
		}
		.mt-5{
			margin-top: -5px;
		}
		.mt-45{
			margin-top: -50px;
		}
		.mt-25{
			margin-top: -25px;
		}
		table th{
			font-size: 10px;
			text-align: left;
		}

		table td{
			font-size: 11px;
			font-weight: bold;
			line-height: 0.8;
		}

		th.total{
			width: 157px;
			font-size: 13px;
			line-height: 0.7;		
		}
		

	</style>
</head>

<body>

	@if($config->logo != null)
	<table>
		<thead>
			<tr>
				<th style="width: 80px">
					<img 
					src="{{ file_exists(public_path('uploads/logos/' . $config->logo)) ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('uploads/logos/' . $config->logo))) : '' }}" 
					alt="Logo" 
					style="height: 100px; margin-top: -20px;"
					>
				</th>
				<th style="text-align: center;">
					<h4 style="text-align:center; " style="margin-top:-10px">{{ $config->nome }}</h4>
					<h4 style="text-align:center; " style="margin-top:-15px">{{ $config->nome_fantasia }}</h4>
					<h4 style="text-align:center; " style="margin-top:-15px">CNPJ: {{ __setMask($config->cpf_cnpj, "###.###.###/####-##") }}</h4>
					<h4 style="text-align:center; " style="margin-top:-15px">Inscrição Estadual: {{ $config->ie }}</h4>


					<h5 style="text-align:center; font-size: 10px; margin-top: -10px;">
						{{ $config->rua }}, {{ $config->numero }}
					</h5>
					<h5 style="text-align:center; font-size: 10px; margin-top: -20px;">
						{{ $config->bairro }} {{ $config->cidade->nome }} ({{ $config->cidade->uf }})
					</h5>
					<h5 style="text-align:center; font-size: 10px; margin-top: -15px;">
						{{ $config->celular }}
					</h5>
				</th>
			</tr>
		</thead>
	</table>

	
	@else
	<h4 style="text-align:center; font-size: 14px" class="mt-10">{{ $config->nome }}</h4>
	<h4 style="text-align:center; font-size: 14px" class="mt-20">{{ $config->nome_fantasia }}</h4>
	<h4 style="text-align:center; font-size: 14px" class="mt-20">CNPJ: {{ __setMask($config->cpf_cnpj, "###.###.###/####-##") }}</h4>
	<h4 style="text-align:center; font-size: 14px" class="mt-20">Inscrição Estadual: {{ $config->ie }}</h4>


	<h5 class="mt-20" style="text-align:center; font-size: 10px;">
		{{ $config->rua }}, {{ $config->numero }}
	</h5>
	<h5 class="mt-20" style="text-align:center; font-size: 10px;">
		{{ $config->bairro }} {{ $config->cidade->nome }} ({{ $config->cidade->uf }})
	</h5>
	<h5 class="mt-10" style="text-align:center; font-size: 10px;">
		{{ $config->celular }}
	</h5>
	@endif

	<div style="margin-left: 10px;">---------------------------------------------------------</div>
	<h4 class="mt-5" style="text-align:center; font-size: 8px;">
		COMPROVANTE DE TROCA
	</h4>
	<div class="mt-10" style="margin-left: 10px;">---------------------------------------------------------</div>
	<label class="mt-5" style="font-size: 9px; margin-left: 5px;">ITENS DA VENDA</label>
	<table>
		<thead>
			<tr>
				<th style="width: 7px">Código</th>
				<th style="width: 145px">Descrição</th>
				<th style="width: 28px">Qtde</th>
				<th style="width: 35px">Vl Unit</th>
				<th style="width: 45px">Vl Total</th>

			</tr>
		</thead>
		<tbody>
			@foreach(($item->nfe ? $item->nfe->itens : $item->nfce->itens) as $i)
			<tr>
				
				<td>{{ $i->produto->numero_sequencial }}</td>
				<td>{{ $i->descricao() }}</td>

				<td>
					@if(!$i->produto->unidadeDecimal())
					{{ number_format($i->quantidade, 0, '.', '') }}
					@else
					{{ number_format($i->quantidade, 3, '.', '') }}
					@endif

				</td>

				<td>{{ __moeda($i->valor_unitario) }}</td>
				<td>{{ __moeda($i->sub_total) }}</td>

			</tr>
			
			@endforeach

			
		</tbody>
	</table>

	<div style="margin-left: 5px;">--------------------</div>

	<label class="mt-5" style="font-size: 9px; margin-left: 5px;">ITENS ALTERADOS</label>
	<table>
		<thead>
			<tr>
				<th style="width: 7px">Código</th>
				<th style="width: 145px">Descrição</th>
				<th style="width: 28px">Qtde</th>
				<th style="width: 35px">Vl Unit</th>
				<th style="width: 45px">Vl Total</th>

			</tr>
		</thead>
		<tbody>
			@foreach($item->itens as $i)
			<tr>
				
				<td>{{ $i->produto->numero_sequencial }}</td>
				<td>{{ $i->descricao() }}</td>

				<td>
					@if(!$i->produto->unidadeDecimal())
					{{ number_format($i->quantidade, 0, '.', '') }}
					@else
					{{ number_format($i->quantidade, 3, '.', '') }}
					@endif

				</td>

				<td>{{ __moeda($i->valor_unitario) }}</td>
				<td>{{ __moeda($i->sub_total) }}</td>

			</tr>
			
			@endforeach

			
		</tbody>
	</table>

	<div class="" style="margin-left: 3px;">------------------------------------------------------------</div>

	<table>
		<thead>
			<tr>
				<th class="total">Qtde de linhas:</th>
				<th class="total" style="text-align: right;">{{ sizeof($item->itens) }}</th>
			</tr>
			<tr>
				<th class="total">Qtde total de itens:</th>
				<th class="total" style="text-align: right;">{{ $item->itens->sum('quantidade') }}</th>
			</tr>

			<tr>
				<th class="total">Valor Total:</th>
				@isset($preVenda)
				<th class="total" style="text-align: right;">R${{ __moeda($item->valor_total) }}</th>
				@else
				<th class="total" style="text-align: right;">R${{ __moeda($item->total) }}</th>
				@endif
			</tr>
			<tr>
				<th class="total">Desconto:</th>
				<th class="total" style="text-align: right;">R${{ __moeda($item->desconto) }}</th>
			</tr>
			<tr>
				<th class="total">Acréscimo:</th>
				<th class="total" style="text-align: right;">R${{ __moeda($item->acrescimo) }}</th>
			</tr>
			@if($item->valor_entrega > 0)
			<tr>
				<th class="total">Frete:</th>
				<th class="total" style="text-align: right;">R${{ __moeda($item->valor_entrega) }}</th>
			</tr>
			@endif

			@if($item->valor_frete > 0)
			<tr>
				<th class="total">Valor do Frete</th>
				<th class="total" style="text-align: right;">R${{ __moeda($item->valor_frete) }}</th>
			</tr>
			@endif
		</thead>
	</table>
	<div class="" style="margin-left: 3px; margin-top: -10px;">------------------------------------------------------------</div>
	<table>
		<thead>
			<tr>
				<th class="total">FORMA PAGAMENTO</th>
				<th class="total" style="text-align: right;">VALOR PAGO</th>
			</tr>

			<tr>
				<th class="total">{{ \App\Models\Nfce::getTipoPagamento($item->tipo_pagamento) }}</th>
				<th class="total" style="text-align: right;">R${{ __moeda($item->valor_troca) }}</th>
			</tr>
			

			<tr>
				<th class="total">Data da venda</th>
				<th class="total" style="text-align: right;">{{ __data_pt($item->nfce ? $item->nfce->created_at : $item->nfe->created_at ) }}</th>
			</tr>
			<tr>
				<th class="total">Data da troca</th>
				<th class="total" style="text-align: right;">{{ __data_pt($item->created_at) }}</th>
			</tr>


			<tr>
				<th class="total">Código da venda</th>
				<th class="total" style="text-align: right;">{{ $item->nfce ? $item->nfce->numero_sequencial : $item->nfe->numero_sequencial }}</th>
			</tr>
			<tr>
				<th class="total">Código da troca</th>
				<th class="total" style="text-align: right;">{{ $item->numero_sequencial }}</th>
			</tr>

			@if(isset($item->nfe->cliente))
			<tr>
				<th colspan="2">{{ $item->nfe->cliente->info }}</th>
			</tr>
			@endif

			@if(isset($item->nfce->cliente))
			<tr>
				<th colspan="2">{{ $item->nfce->cliente->info }}</th>
			</tr>
			@endif

			@if($item->observacao)
			<tr>
				<th class="total">Observação</th>
				<th class="total" style="text-align: right;">{{ $item->observacao }}</th>
			</tr>
			@endif

			@if($configGeral && $configGeral->mensagem_padrao_impressao_venda != "")
			<tr>
				<th colspan="2">{!! $configGeral->mensagem_padrao_impressao_venda !!}</th>
			</tr>
			@endif
		</thead>
	</table>


</body>
