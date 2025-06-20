@extends('relatorios.default')
@section('content')

@isset($marca)
<h5>Marca: {{$marca->nome}}</h5>
@endisset

@isset($categoria)
<h5>Categoria: {{$categoria->nome}}</h5>
@endisset

<p>Total de registros <strong>{{ sizeof($data) }}</strong></p>
<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>#</th>
			<th>Produto</th>
			<th>Vl. venda</th>
			<th>Vl. compra</th>
			<th>Dt. cadastro</th>
			@if(__countLocalAtivo() > 1)
			<th>Disponibilidade</th>
			@endif

			<th>Estoque</th>
			@if($tipo != '')
			<th>Qtd. vendida</th>
			@endif
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td>
				<img src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('').$item->img))}}" alt="logo" class="mr-3" style="width: 30px">
			</td>
			<td>{{ $item->nome }}</td>
			<td>{{ __moeda($item->valor_unitario) }}</td>
			<td>{{ __moeda($item->valor_compra) }}</td>
			<td>{{ __data_pt($item->created_at) }}</td>
			@if(__countLocalAtivo() > 1)
			<td>
				@foreach($item->locais as $l)
				@if($l->localizacao)
				<strong>{{ $l->localizacao->descricao }}</strong>
				@if(!$loop->last) | @endif
				@endif
				@endforeach
			</td>
			<td>
				@foreach($item->estoqueLocais as $e)
				@if($e->local)
				{{ $e->local->descricao }}:
				<strong class="text-success">
					@if($item->unidade == 'UN' || $item->unidade == 'UNID')
					{{ number_format($e->quantidade, 0) }}
					@else
					{{ number_format($e->quantidade, 3) }}
					@endif
				</strong>
				@endif
				@if(!$loop->last) | @endif
				@endforeach
			</td>

			@else
			<td>{{ $item->estoque ? number_format($item->estoque->quantidade, 2) : '0' }} - {{ $item->unidade }}</td>
			@endif


			@if($tipo != '')
			<td>{{ $item->quantidade_vendida }}</td>
			@endif
		</tr>
		@endforeach
	</tbody>
</table>
@endsection
