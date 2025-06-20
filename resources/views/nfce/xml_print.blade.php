<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">

		.content{
			margin-top: -0px;
		}
		.titulo{
			font-size: 20px;
			margin-bottom: 0px;
			font-weight: bold;
		}

		.b-top{
			border-top: 1px solid #000; 
		}
		.b-bottom{
			border-bottom: 1px solid #000; 
		}

	</style>

</head>
<body>
	<div class="content">
		<center><label class="titulo">{{ $empresa->nome }}</label></center>
		<center><label class="titulo" style="font-size: 17px">Arquivos XML de NFCe</label></center>
	</div>
	<br>
	
	<table>
		<tr>
			
			<td class="" style="width: 525px;">
				CNPJ: <strong>{{ $empresa->cpf_cnpj }}</strong>
			</td>

			<td class="" style="width: 525px;">
				IE: <strong>{{ $empresa->ie }}</strong>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="b-top" style="width: 1050px;">
				Endereço: <strong>{{ $empresa->rua }}, {{ $empresa->numero }} - {{ $empresa->bairro }} - {{ $empresa->cidade->nome }} ({{ $empresa->cidade->uf }})</strong>
			</td>
		</tr>
	</table>

	<table>
		<tr>
			<td class="b-top" style="width: 1050px;">
				Período <strong>{{ __data_pt($start_date, 0) }} à {{ __data_pt($end_date, 0) }}</strong>
			</td>
		</tr>
	</table>
	

	<table>
		<thead>
			<tr>
				<td class="b-bottom" style="width: 350px;">
					Cliente
				</td>
				<td class="b-bottom" style="width: 150px;">
					Data
				</td>
				<td class="b-bottom" style="width: 220px;">
					Chave
				</td>
				<td class="b-bottom" style="width: 100px;">
					Número
				</td>
				<td class="b-bottom" style="width: 80px;">
					Total
				</td>
			</tr>
		</thead>
		
		<tbody>
			@php $total = 0; @endphp
			
			@foreach($dataPrint as $item)
			<tr>
				<th style="text-align: left;">{{ $item->cliente ? $item->cliente->info : 'Consumidor final' }}</th>
				<th style="text-align: left;">{{ __data_pt($item->data_emissao) }}</th>
				<th style="text-align: left;">{{ $item->chave }}</th>
				<th style="text-align: left;">{{ $item->numero }}</th>
				<th style="text-align: left;">{{ __moeda($item->total) }}</th>
			</tr>
			@php $total += $item->total; @endphp

			@endforeach
		</tbody>
	</table>
	<br>

	<table>
		<tr>
			<td class="b-top b-bottom" style="width: 525px;">
				<center><strong>Quantidade de registros: {{ sizeof($dataPrint) }}</strong></center>
			</td>

			<td class="b-top b-bottom" style="width: 525px;">
				<center><strong>Total: R$ {{ __moeda($total) }}
				</strong></center>
			</td>


		</tr>
	</table>

	<br>

</body>
</html>