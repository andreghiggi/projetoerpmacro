<div class="container" style="margin-top: 15px">
	@foreach($data as $item)
	<div class="col-12">
		<input class="radio-frete" type="radio" name="tipo_frete" id="radio" value="{{ $item['tipo'] }}" data-valor="{{ (float)$item['valor'] }}" data-endereco-id="null">
		{{ $item['tipo'] }} R$ {{ __moeda((float)$item['valor']) }}
	</div>
	@endforeach
	@if($config->habilitar_retirada)
	<div class="col-12">
		<input class="radio-frete" type="radio" name="tipo_frete" id="radio" value="0" data-valor="0">
		Retirar na loja
	</div>
	@endif

	@if($config->frete_gratis_valor > 0 && $config->frete_gratis_valor <= $total)
	<div class="col-12">
		<input class="radio-frete" type="radio" name="tipo_frete" id="radio" value="gratis" data-valor="0">
		Frete grátis
	</div>
	@endif
</div>