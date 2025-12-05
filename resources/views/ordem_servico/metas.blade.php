@extends('layouts.app', ['title' => 'Metas de Ordem de Serviço'])
@section('content')
<div class="mt-1">
	<div class="row">
		<div class="card">
			<div class="card-body">

				<h5>META GERAL MÊS: <strong>R$ {{ __moeda($totalMeta) }}</strong></h5>
				<h5>TOTAL OS MÊS: <strong>R$ {{ __moeda($somaOsMes) }}</strong></h5>

				<div class="progress-w-percent">
					<span class="progress-value fw-bold">{{ __calcPercentual($totalMeta, $somaOsMes) }}%</span>
					<div class="progress progress-sm">
						<div class="progress-bar bg-success" style="width: {{ __calcPercentual($totalMeta, $somaOsMes) }}%;" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>

				<span class="c-circular-progress c-circular-progress--4"></span>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-3">
						<label>Funcionário</label>
						<select class="form-select" id="inp-meta_id">
							<option value="">Selecione</option>
							@foreach($metas as $m)
							<option value="{{ $m->id }}">{{ $m->funcionario->nome }}</option>
							@endforeach
						</select>
					</div>

					<div class="col-md-9 metas">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/meta_ordem_servico.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection
