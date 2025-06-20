<div class="row">
	<table class="table table-dynamic table-variacao">
		<thead class="table-success">
			<tr>
				<th>Imagem</th>
				<th>Descrição</th>
				<th>Valor</th>
				<th>Código de barras</th>
				<th>Referência</th>
				<th>Estoque</th>
				<th>

				</th>
			</tr>
		</thead>
		<tbody>
			@foreach($item->variacoes as $i)
			<tr class="dynamic-form">
				<td>
					<img class="img-round" src="{{ $i->img }}">
				</td>
				<td>
					{{ $i->descricao }}
				</td>
				<td>
					{{ __moeda($i->valor) }}
				</td>
				<td>
					{{ $i->codigo_barras }}
				</td>
				<td>
					{{ $i->referencia }}
				</td>
				<td>
					{{ $i->estoque ? number_format($i->estoque->quantidade, 0) : '' }}
				</td>
				<td>
					<button type="button" onclick="selecionarVariacao('{{ $i->id }}', '{{ $i->descricao }}', '{{ $i->valor }}')" class="btn btn-sm btn-success">
						<i class="ri-check-line"></i>
					</button>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>