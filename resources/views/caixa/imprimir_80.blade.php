<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		.footer {
			position: fixed;
			bottom: 0px;
			padding: 0;
		}
		.footer small{
			color:grey; 
			font-size: 10px;
			text-align: left;
			margin-top: 100px !important;
		}
		body{
			width: 260px;
			/*background: #000;*/
			margin-left: -40px;
			margin-top: -40px;
		}
		.mt-20{
			margin-top: -20px;
		}
		.mt-10{
			margin-top: -10px;
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
		}

		strong{
			color: #3B4CA7;
		}

	</style>
</head>
<header>
	<div class="headReport">
	</div>
</header>
<body>
	<h5 style="text-align:center;" class="mt-10">Relatório de caixa</h5>
	<h5 style="text-align:center;" class="mt-20">{{ $config->nome_fantasia }}</h5>
	<h5 style="text-align:center;" class="mt-20">{{ __setMask($config->cpf_cnpj) }}</h5>
	
	<table>
		<tbody>
			<tr>
				<td>
					<h6 style="text-align:left; font-size: 8px;" class="mt-20">Total de vendas: <strong>R$ {{ number_format($totalVendas, 2, ',', '.') }}</strong></h6>
				</td>
				<td>
					<h6 style="margin-left:20px; font-size: 8px;" class="mt-20">Total de compras: <strong>R$ {{ number_format($totalCompras, 2, ',', '.') }}</strong></h6>
				</td>
			</tr>
			<tr>
				<td>
					<h6 style="text-align:left; font-size: 8px;" class="mt-25">Abertura: <strong>{{ __data_pt($item->created_at) }}</strong></h6>
				</td>
				<td>
					<h6 style="margin-left:20px; font-size: 8px;" class="mt-25">Fechamento: <strong>{{ __data_pt($item->updated_at_at) }}</strong></h6>
				</td>
			</tr>

		</tbody>
	</table>

	<h6 style="text-align:left; border-top: 1px solid #000;" class="mt-20">Total por tipo de pagamento:</h6>
	<table>
		<tbody>
			@foreach($somaTiposPagamento as $key => $tp)
			@if($tp > 0)
			<tr>
				<td>
					<h6 style="text-align:left; font-size: 9px; width: 150px;" class="mt-25">{{App\Models\Nfce::getTipoPagamento($key)}}</h6>
				</td>
				<td>
					<h6 style="text-align:left; font-size: 9px;" class="mt-25">R$ {{ __moeda($tp) }}</h6>
				</td>
			</tr>
			@endif
			@endforeach
		</tbody>
	</table>

	<h6 style="text-align:left; border-top: 1px solid #000;" class="mt-20">Vendas:</h6>
	<table>
		<tbody>
			<thead>
				<tr>
					<th>
						<h6 style="text-align:left; font-size: 9px; width: 107px;" class="mt-25">CLIENTE</h6>
					</th>

					<th>
						<h6 style="text-align:left; font-size: 9px; width: 70px;" class="mt-25">DATA</h6>
					</th>
					<th>
						<h6 style="text-align:left; font-size: 9px; width: 100px;" class="mt-25">TIPO DE PAG.</h6>
					</th>
				</tr>
				<tr>
					<th>
						<h6 style="text-align:left; font-size: 9px; width: 70px;" class="mt-25">ESTADO</h6>
					</th>
					<th>
						<h6 style="text-align:left; font-size: 9px; width: 70px;" class="mt-25">NFCE/NFE</h6>
					</th>
					<th>
						<h6 style="text-align:left; font-size: 9px; width: 70px;" class="mt-25">VALOR</h6>
					</th>
				</tr>
			</thead>

			<tbody>
				
				@foreach($data as $v)
				<tr>
					<td>
						<h6 style="text-align:left; font-size: 9px; width: 107px;" class="mt-25">{{ $v->cliente->razao_social ?? 'NÃO IDENTIFCADO' }}</h6>
					</td>

					<td>
						<h6 style="text-align:left; font-size: 9px; width: 70px;" class="mt-25">{{ __data_pt($v->created_at) }}</h6>
					</td>
					<td>
						<h6 style="text-align:left; font-size: 9px; width: 100px;" class="mt-25">
							@if($v->tipo_pagamento == '99')
							Outros
							@else
							{{ $v->tipo_pagamento ? $v->getTipoPagamento($v->tipo_pagamento) : 'Pag Múltiplo'}}
							@endif
						</h6>
					</td>
				</tr>

				<tr>
					<td>
						<h6 style="text-align:left; font-size: 9px; width: 107px; border-bottom: 1px solid #999;" class="mt-25">{{ $v->tipo != 'OS' ? strtoupper($v->estado) : '' }}</h6>
					</td>
					<td>
						<h6 style="text-align:left; font-size: 9px; width: 70px; border-bottom: 1px solid #999;" class="mt-25">
							@if($v->estado == 'aprovado')
							@if($v->tipo == 'Nfe')
							{{ $v->numero > 0 ? $v->numero : '--' }}
							@else
							{{ $v->numero > 0 ? $v->numero : '--' }}
							@endif
							@else
							--
							@endif
						</h6>
					</td>
					
					<td>
						<h6 style="text-align:left; font-size: 9px; width: 70px; border-bottom: 1px solid #999;" class="mt-25">
							R$@if($v->tipo != 'OS')
							{{ __moeda($v->total) }}
							@else
							{{ __moeda($v->valor) }}
							@endif
						</h6>
					</td>
				</tr>

				
				@endforeach
			</tbody>
		</tbody>
	</table>


	@php
	$somaSuprimento = 0;
	$somaSangria = 0;
	@endphp

	<h6 style="text-align:left; border-top: 1px solid #000;" class="mt-20">Suprimentos:</h6>
	<table>
		<tbody>
			@if(sizeof($suprimentos) > 0)
			@foreach($suprimentos as $s)

			@php
			$somaSuprimento += $s->valor;
			@endphp 
			<tr>
				<td>
					<h6 style="text-align:left; font-size: 9px; width: 150px;" class="mt-25">
						{{ __data_pt($s->created_at) }}
					</h6>
				</td>
				<td>
					<h6 style="text-align:left; font-size: 9px;" class="mt-25">R$ {{ __moeda($s->valor) }}</h6>
				</td>
			</tr>

			@endforeach
			@else
			<tr>
				<td>
					<h6 style="text-align:left; font-size: 9px; width: 150px;" class="mt-25">
						--
					</h6>
				</td>
				<td>
					<h6 style="text-align:left; font-size: 9px;" class="mt-25">R$ {{ __moeda(0) }}</h6>
				</td>
			</tr>
			@endif
		</tbody>
	</table>

	<h6 style="text-align:left; border-top: 1px solid #000;" class="mt-20">Sangrias:</h6>
	<table>
		<tbody>
			@if(sizeof($sangrias) > 0)
			@foreach($sangrias as $s)

			@php
			$somaSangria += $s->valor;
			@endphp
			<tr>
				<td>
					<h6 style="text-align:left; font-size: 9px; width: 150px;" class="mt-25">
						{{ __data_pt($s->created_at) }}
					</h6>
				</td>
				<td>
					<h6 style="text-align:left; font-size: 9px;" class="mt-25">R$ {{ __moeda($s->valor) }}</h6>
				</td>
			</tr>

			@endforeach
			@else
			<tr>
				<td>
					<h6 style="text-align:left; font-size: 9px; width: 150px;" class="mt-25">
						--
					</h6>
				</td>
				<td>
					<h6 style="text-align:left; font-size: 9px;" class="mt-25">R$ {{ __moeda(0) }}</h6>
				</td>
			</tr>
			@endif
		</tbody>
	</table>

	<table>
		<tbody>
			<tr>
				<td style="width: 115px;">
					<h6 style="text-align:left; font-size: 9px;" class="mt-20">
						Soma de vendas: <strong>R$ {{ __moeda($totalVendas) }}</strong>
					</h6>
				</td>
				<td style="width: 130px;">
					<h6 style="float: right; font-size: 9px;" class="mt-20">Soma de sangria: 
						<strong>R$ {{ __moeda($somaSangria) }}</strong>
					</h6>
				</td>
			</tr>
			<tr>
				<td style="width: 115px;">
					<h6 style="text-align:left; font-size: 9px;" class="mt-20">
						Soma de suprimento: <strong>R$ {{ __moeda($somaSuprimento) }}</strong>
					</h6>
				</td>
				<td style="width: 130px;">
					<h6 style="float: right; font-size: 9px;" class="mt-20">Valor em caixa: 
						<strong>R$ {{ __moeda($somaSuprimento + $totalVendas - $somaSangria) }}</strong>
					</h6>
				</td>
			</tr>
			<tr>
				<td style="width: 115px;">
					<h6 style="text-align:left; font-size: 9px;" class="mt-20">
						Contagem gaveta: <strong>R$ {{ __moeda($item->valor_dinheiro) }}</strong>
					</h6>
				</td>
				<td style="width: 130px;">
					<h6 style="float: right; font-size: 9px;" class="mt-20">Soma de serviços: 
						<strong>R$ {{ __moeda($somaServicos) }}</strong>
					</h6>
				</td>
			</tr>

		</tbody>
	</table>
	<h6 style="text-align:left; border-top: 1px solid #000;" class="mt-10">PRODUTO VENDIDOS:</h6>
	<table>

		<thead>
			<tr>
				<th>
					<h6 style="text-align:left; font-size: 9px; width: 147px;" class="mt-25">PRODUTO</h6>
				</th>

				<th>
					<h6 style="text-align:left; font-size: 9px; width: 107px;" class="mt-25">QUANTIDADE</h6>
				</th>

			</tr>
			<tr>
				<th>
					<h6 style="text-align:left; font-size: 9px; width: 127px;" class="mt-25">VALOR VENDA</h6>
				</th>
				<th>
					<h6 style="text-align:left; font-size: 9px; width: 107px;" class="mt-25">VALOR COMPRA</h6>
				</th>

			</tr>
		</thead>
		<tbody>
			@foreach($produtos as $p)
			<tr>
				<td>
					<h6 style="text-align:left; font-size: 9px; width: 147px;" class="mt-25">
						{{ $p['nome'] }}
					</h6>
				</td>
				<td>
					<h6 style="text-align:left; font-size: 9px; width: 107px;" class="mt-25">
						{{ $p['quantidade'] }}
					</h6>
				</td>
			</tr>
			<tr>
				<td>
					<h6 style="text-align:left; font-size: 9px; width: 147px; border-bottom: 1px solid #999;" class="mt-25">
						{{ __moeda($p['valor_venda']) }}
					</h6>
				</td>
				<td>
					<h6 style="text-align:left; font-size: 9px; width: 107px; border-bottom: 1px solid #999;" class="mt-25">
						{{ __moeda($p['valor_compra']) }}
					</h6>
				</td>
			</tr>
			@endforeach
		</tbody>

	</table>
	<br>
	<table>
        <tr>
            <td>
                ________________________________________
            </td>
        </tr>
        <tr>
            <td>
                {{$usuario->name}} - {{ date('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

</body>
