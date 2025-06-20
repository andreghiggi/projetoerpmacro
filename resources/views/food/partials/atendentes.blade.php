<div class="col-md-12">
	@if(sizeof($funcionarios) > 1)
	<div class="row">

		<div class="col-md-12 mt-1">
			<h5>Atendentes</h5>
		</div>
		<div class="col-md-2 mt-1">
			<button data-id="0" class="botao-acao-gray active btn-atendente" style="width: 100%" type="button">Todos</button>
		</div>
		@foreach($funcionarios as $f)
		<div class="col-md-2 mt-1">
			<button data-id="{{ $f['id'] }}" class="botao-acao-gray btn-atendente" style="width: 100%" type="button">{{ $f['nome'] }}</button>
		</div>
		@endforeach
	</div>
	@endif
	<div class="row" style="margin-top: 10px;">
		@if(sizeof($data) > 0)
		<div class="col-md-12 mt-1">
			<h5 id="lbl-horarios">Horários disponíveis</h5>
		</div>
		@foreach($data as $item)
		<div class="col-md-4 card-horario card_{{ $item['funcionario_id'] }}" style="margin-top: 10px">
			<div class="card">
				<h5>Atendente: <strong>{{ $item['funcionario_nome'] }}</strong></h5>
				<h6>{{ __data_pt($item['data'], 0) }} {{ $item['inicio'] }}</h6>
				<h6>Previsão de término {{ $item['fim'] }}</h6>
				<h6><strong>{{ \App\Models\DiaSemana::getDiaStr($item['dia_semana']) }}</strong></h6>

				<input type="hidden" class="funcionario_id" value="{{ $item['funcionario_id'] }}">
				<input type="hidden" class="inicio" value="{{ $item['inicio'] }}">
				<input type="hidden" class="fim" value="{{ $item['fim'] }}">
				<input type="hidden" class="data" value="{{ $item['data'] }}">
				<input type="hidden" class="data_pt" value="{{ __data_pt($item['data'], 0) }}">
				<input type="hidden" class="funcionario_nome" value="{{ $item['funcionario_nome'] }}">
				<button type="button" class="botao-acao botao-agendamento">Selecionar</button>
			</div>
		</div>
		@endforeach
		@else
		<div class="col-md-12 mt-1">
			<h5 id="lbl-horarios">Nada encontrado para essa data!</h5>
		</div>
		@endif
		
	</div>
</div>