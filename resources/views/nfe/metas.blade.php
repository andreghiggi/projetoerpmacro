@extends('layouts.app', ['title' => 'Metas de Venda'])

@section('content')
<div class="mt-1">
	<div class="row">
		<div class="card">
			<div class="card-body">

				<div class="row">
					<div class="col-md-2">
						<label>Período</label>
						<select class="form-select" id="inp-periodo">
							@foreach($periodosMeta as $p)
							<option selected value="{{ $p }}">{{ $p }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<br>
				<h5>META GERAL MÊS: <strong class="meta-geral">R$ {{ __moeda($totalMeta) }}</strong></h5>
				<h5>TOTAL VENDIDO MÊS: <strong class="total-mes">R$ {{ __moeda(0) }}</strong></h5>

				<div class="progress-w-percent">
					<span class="progress-value fw-bold"></span>
					<div class="progress progress-lg">
						<div class="progress-bar bg-success" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>

				<div class="valor-meta-superior d-none">
					<h4>Soma das vendas do mês superou a meta estipulada!</h4>
					<h5>Total vendido - total da meta: <strong class="total-diferenca text-success">R$ {{ __moeda(0) }}</strong></h5>
				</div>

				<div class="valor-meta-baixo d-none">
					<h4>Soma das vendas ainda não superou a meta estipulada!</h4>
					<h5>Total da meta - Total vendido: <strong class="total-diferenca text-danger">R$ {{ __moeda(0) }}</strong></h5>
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
<script type="text/javascript" src="/js/meta_venda.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection
