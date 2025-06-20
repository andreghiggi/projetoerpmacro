<p class="text-muted">Clique no sabor para adicionar</p>
@foreach($data as $p)
@if($p->valor_pizza > 0 && $p->id != $pizzaPrincipal->id)
<div class="row box-sabor" style="margin-top: 7px; box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;" onclick="seleciona_tamanho('{{ $p->id }}')">
	<div class="col-md-3 col-12">
		<img style="height: 50px; border-radius: 10px; margin-top: 7px; margin-bottom: 7px;" src="{{ $p->img }}">
	</div>
	<div class="col-md-6 col-8" style="margin-top: 20px;">
		<label>{{ $p->nome }}</label>
	</div>
	<div class="col-md-3 col-4" style="margin-top: 20px;">
		<label>R$ {{ __moeda($p->valor_pizza) }}</label>
	</div>
</div>
@endif
@endforeach

