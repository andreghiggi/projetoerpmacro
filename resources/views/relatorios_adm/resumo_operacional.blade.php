@extends('relatorios_adm.default')
@section('content')

@if($start_date && $end_date)
<p>Período filtrado <strong>{{ __data_pt($start_date, 0) }}</strong> à <strong>{{ __data_pt($end_date, 0) }}</strong></p>
@endif
<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>Empresa</th>
			<th>CPF/CNPJ</th>
			<th>Endereço</th>
			<th>Telefone</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key => $item)
		<tr class="pure-table-odd">
			<td>{{ $item->empresa }}</td>
			<td>{{ $item->cpf_cnpj }}</td>
			<td>{{ $item->endereco }}</td>
			<td>{{ $item->celular }}</td>
		</tr>
		<tr>
			<td colspan="4">
				<table>
					<tbody>
						<tr>
							<td style="width: 180px; text-align: left"></td>
							<td style="width: 140px; text-align: left">Vendas</td>
							<td style="width: 140px; text-align: left">Compras</td>
							<td style="width: 140px; text-align: left">Contas Recebidas</td>
							<td style="width: 140px; text-align: left">Contas Receber</td>
							<td style="width: 140px; text-align: left">Contas Pagas</td>
							<td style="width: 140px; text-align: left;">Contas Pagar</td>
						</tr>
						<tr>
							<td style="text-align: left;">Nº de registros</td>
							<td style="text-align: left;">{{ $item->contador_vendas }}</td>
							<td style="text-align: left;">{{ $item->contador_compras }}</td>
							<td style="text-align: left;">{{ $item->contador_conta_recebidas }}</td>
							<td style="text-align: left;">{{ $item->contador_conta_receber }}</td>
							<td style="text-align: left;">{{ $item->contador_conta_pagas }}</td>
							<td style="text-align: left;">{{ $item->contador_conta_pagar }}</td>
						</tr>
						<tr>
							<td style="text-align: left; background-color: #3d3b3b; color: #fff;">Valor total</td>
							<td style="text-align: left; background-color: #3d3b3b; color: #fff;">R${{ __moeda($item->soma_vendas) }}</td>
							<td style="text-align: left; background-color: #3d3b3b; color: #fff;">R${{ __moeda($item->soma_compras) }}</td>
							<td style="text-align: left; background-color: #3d3b3b; color: #fff;">R${{ __moeda($item->soma_conta_recebidas) }}</td>
							<td style="text-align: left; background-color: #3d3b3b; color: #fff;">R${{ __moeda($item->soma_conta_receber) }}</td>
							<td style="text-align: left; background-color: #3d3b3b; color: #fff;">R${{ __moeda($item->soma_conta_pagas) }}</td>
							<td style="text-align: left; background-color: #3d3b3b; color: #fff;">R${{ __moeda($item->soma_conta_pagar) }}</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		@endforeach
	</tbody>
	
</table>
<h4>Total de empresas: <strong style="color: #3B4CA7">{{ sizeof($data) }}</strong></h4>

@endsection
