<div class="row">
	<h4>Você possui uma fatura em aberto</h4>
	<h5>Vencimento: <strong>{{ __data_pt($item->vencimento, 0) }}</strong></h5>
	<h5>Valor: <strong>R$ {{ __moeda($item->valor) }}</strong></h5>
	<div class="col-md-4">
		<a class="btn btn-dark btn-sm mt-1" target="_blank" href="{{ $item->pdf_boleto }}">
			<i class="ri-printer-line"></i>
			Ver boleto
		</a>
	</div>
	
</div>