@forelse($data as $item)
<div class="card-suspensa">

	<div class="cs-info">
		<div class="cs-cliente"><i class="ri-user-line"></i> {{ $item->cliente ? $item->cliente->razao_social : ($item->cliente_nome != "" ? $item->cliente_nome : "--") }}</div>
		<div class="cs-data"><i class="ri-calendar-line"></i> {{ __data_pt($item->created_at) }}</div>
	</div>

	<div class="cs-total">
		<span>Total:</span>
		<strong>R$ {{ __moeda($item->total) }}</strong>
	</div>

	<div class="cs-acoes">
		<a class="btn btn-dark btn-sm cs-btn-finalizar" onclick="abrirPopupImpressao('{{ route('frontbox.imprimir-nao-fiscal', $item->id) }}')">
			<i class="ri-printer-line"></i> Imprimir cupom
		</a>
		
		@if($item->estado == 'aprovado')
		<a class="btn btn-success btn-sm cs-btn-finalizar" onclick="abrirPopupImpressao('{{ route('nfce.imprimir', $item->id) }}')">
			<i class="ri-printer-line"></i> Imprimir NFCe
		</a>

		@endif

	</div>

</div>
@empty

<label class="text-center w-100 m-2">Nenhuma venda</label>

@endforelse

