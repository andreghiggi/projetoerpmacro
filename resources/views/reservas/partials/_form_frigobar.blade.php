
<div class="row g-2 m-2">
	<div class="col-md-12">
		<h5 class="text-danger">Frigobar <strong>{{ $frigobar->modelo }}</strong></h5>
	</div>

	<div class="table-responsive mt-2">
		<table class="table" id="table-produtos">
			<thead class="table-dark">
				<tr>
					<th></th>
					<th>Produto</th>
					<th>Qtd. disponível</th>
					<th>Qtd. consumida</th>
					<th>Valor unitário</th>
					
				</tr>
			</thead>
			<tbody>
				@foreach($frigobar->padraoProdutos as $prod)
				<input type="hidden" name="item_id[]" value="{{ $prod->id }}">
				<tr>
					<td><img class="img-60" src="{{ $prod->produto->img }}"></td>
					<td><label style="width:350px">{{ $prod->produto->nome }}</label></td>
					<td>{{ $prod->quantidade }}</td>
					<td>
						<input type="tel" class="form-control quantidade" name="quantidade[]" value="0">
					</td>
					<td>
						<input type="tel" class="form-control moeda" name="valor_unitario[]" value="{{ __moeda($prod->produto->valor_unitario) }}">
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

	</div>
</div>

<div class="col-12" style="text-align: right;">
	<button type="submit" class="btn btn-success btn-action px-5" id="btn-store">Salvar</button>
</div>