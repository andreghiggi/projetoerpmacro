<div class="autocomplete-list" onclick="fecharAutocomplete()">
		<span class="autocomplete-result"> 
		<i class="ri-close-line"></i> 
		Fechar
	</span>
</div>
@foreach($data as $p)
<div class="autocomplete-list" onclick="addProduto('{{$p->id}}', '{{$p->variacao_modelo_id}}')">
	<span class="autocomplete-result"> 
		<i class="ri-search-line"></i> 
		[{{ $p->numero_sequencial }}] - @if($p->referencia) REF{{ $p->referencia }} @endif {{ $p->codigo_barras }} ({{ $p->nome }}) 
		@if($p->variacao_modelo_id == null)
		R$ {{ __moeda($p->valor_unitario) }}
		@else
		
		@endif
	</span>
</div>
@endforeach