<div class="row">
	<div class="col-md-6 col-12">
		<h5>Produto: <strong>{{ $item->produto->nome }}</strong></h5>
		<h5>Quantidade: 
			<strong>
				@if($item->produto->unidade == 'UN' || $item->produto->unidade == 'UNID')
				{{ number_format($item->quantidade, 0) }}
				@else
				{{ number_format($item->quantidade, 3) }}
				@endif
			</strong>
		</h5>
	</div>
	<div class="col-md-6 col-12">
		<h5>Valor de compra: <strong>R$ {{ __moeda($item->produto->valor_compra) }}</strong></h5>
		<h5>Valor de venda: <strong>R$ {{ __moeda($item->produto->valor_unitario) }}</strong></h5>
	</div>

</div>