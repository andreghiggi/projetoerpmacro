@foreach($produtos as $p)
<div class="col-md-2 produto" ondblclick="infoProduto('{{$p->id}}')" onclick="addProduto('{{$p->id}}')">
	
	<div class="card" style="height: 200px">
		<span class="stock-item"><b>{{ $p->estoqueAtual() }} {{ $p->unidade }}</b></span>
		@if($p->precoComPromocao())
		<div class="ribbon" aria-hidden="true">Promoção</div>
		@endif
		<img class="card-img-top" src="{{ $p->img }}" alt="{{ $p->nome }}">
		<div class="card-body body-item">
			<h4 class="card-title">{{ substr($p->nome, 0, 30) }}</h4>
			@if($p->precoComPromocao())
			<p class="card-text text-success"> R$ {{ __moeda($p->precoComPromocao()->valor) }}</p>
			@else
			@if(isset($lista_id) && $lista_id)
			
			@if($p->itemListaView($lista_id))
			<p class="card-text text-success">R$ {{ __moeda($p->itemListaView($lista_id)->valor) }}</p>
			@endif

			@else

			<p class="card-text text-success">R$ {{ __moeda(__valorProdutoLocal($p, $local_id)) }}</p>
			
			@endif
			@endif
		</div>
	</div>
</div>
@endforeach
<div class="mt-1 d-flex justify-content-center produtos-pagination">
	{!! $produtos->links() !!}
</div>