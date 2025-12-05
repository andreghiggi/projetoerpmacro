<div class="row">
	<hr style="margin-top: 10px;">
	<div class="">
		<label>Tamanho</label>
		<select class="form-control form-select sangria-select" id="tamanhoPizza">
			<option value="">Selecione o tamanho</option>
			@foreach($tamanhosPizza as $t)
			<option value="{{ $t->id }}" data-max_sabores="{{ $t->maximo_sabores }}">{{ $t->nome }} - até {{ $t->maximo_sabores }} sabor(es)</option>
			@endforeach
		</select>
	</div>
</div>
<div class="containerSabores d-none">
	<input type="text" id="filtrarSabores" class="form-control mb-2 mt-2" placeholder="Buscar sabor...">

	<div class="sabores-wrapper">
		@foreach($sabores as $key => $s)
		<div class="sabor-item sabor_{{ $s['id'] }} @if($key == 0) selecionado bloqueado @else @if($s['selecionado'] == 1) selecionado @endif @endif" data-id="{{ $s['id'] }}" data-valores='@json($s["valores"])'>

			@if($s['img'])
			<img src="{{ $s['img'] }}" alt="{{ $s['nome'] }}">
			@else
			<img src="/noimg.png" alt="Sem imagem">
			@endif

			<div class="sabor-nome">{{ $s['nome'] }}</div>
		</div>
		@endforeach
	</div>

	<div class="total-adicional-card">
		<span class="label">Total da pizza</span>
		<span class="valor">R$ <span id="valorPizza">0,00</span></span>
	</div>
</div>