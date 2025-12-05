@foreach($produtos as $p)
	<tr data-id="{{ $p->id }}">
		<td><label style="width: 400px;">{{ $p->nome }}</label></td>
		<td>{{ $p->referencia ?? '--' }}</td>
		<td>{{ $p->codigo_barras ?? '--' }}</td>
		<td>{{ $p->categoria ? $p->categoria->nome : '--' }}</td>
		<td>{{ $p->marca ? $p->marca->nome : '--' }}</td>
		<td>
			<label class="text-muted">Atual R$ {{ __moeda($p->valor_unitario) }}</label>
			<input style="width: 150px;" type="tel" class="form-control moeda input-edit" name="valor_venda" value="{{ __moeda($p->valor_unitario) }}">
		</td>
		<td>
			<label class="text-muted">Atual R$ {{ __moeda($p->valor_compra) }}</label>
			<input style="width: 150px;" type="tel" class="form-control moeda input-edit" name="valor_compra" value="{{ __moeda($p->valor_compra) }}">
		</td>
		<td>
			<label class="text-muted">Atual {{ $p->estoque ? (!$p->unidadeDecimal() ? number_format($p->estoque->quantidadeLocal($local_id), 0, '.', '') : number_format($p->estoque->quantidadeLocal($local_id), 3, '.', '')) : 0 }}</label>
			<input style="width: 150px;" type="tel" class="form-control quantidade input-edit" name="quantidade_estoque" value="{{ $p->estoque ? (!$p->unidadeDecimal() ? number_format($p->estoque->quantidadeLocal($local_id), 0, '.', '') : number_format($p->estoque->quantidadeLocal($local_id), 3, '.', '')) : 0 }}">
		</td>
	</tr>
@endforeach