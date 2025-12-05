<div class="row">
	<div class="col-md-6">
		<h4>{{ $item->nome }}</h4>
		<h5>Valor de venda: <strong>R$ {{ __moeda($item->valor_unitario) }}</strong></h5>
		<h5>Valor de compra: <strong>R$ {{ __moeda($item->valor_compra) }}</strong></h5>
		<h5>Data de cadastro: <strong>{{ __data_pt($item->created_at) }}</strong></h5>
		<h5>Unidade: <strong>{{ $item->unidade }}</strong></h5>
		<h5>Categoria: <strong>{{ $item->categoria ? $item->categoria->nome : '--' }}</strong></h5>
		<h5>Código de barras: <strong>{{ $item->codigo_barras ? $item->codigo_barras : '--' }}</strong></h5>
		
	</div>
	<div class="col-md-6">
		<img src="{{ $item->img }}" style="height: 100px; border-radius: 8px;">
	</div>
</div>