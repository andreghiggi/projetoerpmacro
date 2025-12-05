<div class="row g-2">
	<h4 class="col-12">{{ $item->nome }}</h4>
	<input type="hidden" id="inp-id-item" value="{{ $item->id }}">
	<input type="hidden" id="inp-code-item" value="{{ $code }}">
	<div class="col-md-6">
		<label>Quantidade</label>
		<input type="tel" class="form-control quantidade" id="inp-qtd-item" value="{{ $quantidade }}">
	</div>
	<div class="col-md-6">
		<label>Valor unitário</label>
		<input type="tel" class="form-control moeda" id="inp-valor-unitario-item" value="{{ $valor_unitario }}">
	</div>
</div>