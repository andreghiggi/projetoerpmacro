<div class="row p-3">
	<h5>META GERAL MÊS: <strong>R$ {{ __moeda($totalMeta) }}</strong></h5>
	<h5>TOTAL VENDIDO MÊS: <strong>R$ {{ __moeda($somaVendasMes) }}</strong></h5>
	<div class="progress-w-percent">
		<span class="progress-value fw-bold">{{ __calcPercentual($totalMeta, $somaVendasMes) }}%</span>
		<div class="progress progress-sm">
			<div class="progress-bar bg-danger" style="width: {{ __calcPercentual($totalMeta, $somaVendasMes) }}%;" aria-valuemin="0" aria-valuemax="100"></div>
		</div>
	</div>

	<div id="chart">
	</div>
</div>