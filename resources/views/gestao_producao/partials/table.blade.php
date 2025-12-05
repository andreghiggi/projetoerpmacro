@foreach($produto->composicao as $c)
<tr>
	<td>
		{{ $c->ingrediente->nome }}
		<input type="hidden" value="{{ $c->ingrediente_id }}" class="produto_id" name="produto_id[]">

	</td>
	<td>
		<input type="tel" readonly class="form-control moeda" name="quantidade_produto[]" value="{{ !$c->ingrediente->unidadeDecimal() ? number_format($c->quantidade, 0, ',', '') : number_format($c->quantidade, 2, ',', '') }}">
	</td>

	<td>
		<input type="tel" class="form-control moeda valor_unitario_produto" name="valor_unitario_produto[]" value="{{ __moedaInput($c->ingrediente->valor_compra) }}">
	</td>
	<td>
		<input type="tel" class="form-control moeda sub_total_produto" name="sub_total_produto[]" value="{{ __moedaInput($c->ingrediente->valor_compra * $c->quantidade) }}">
	</td>

	<td>
		<input type="tel" class="form-control moeda" name="observacao_produto[]">
	</td>
</tr>
@endforeach

<tr>
	<td class="text-danger">Disponibilidade em estoque</td>
	<td class="text-danger">{{ $disponibilidade }}</td>
</tr>
