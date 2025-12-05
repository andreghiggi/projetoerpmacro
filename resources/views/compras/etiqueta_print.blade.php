<button class="btn btn-success" onclick="window.print()">Imprimir</button>
<div id="preview_body">
	@php
	$contLinha = 0;
	@endphp
	@foreach($data as $item)
	<div style="height: {{$altura}}mm; !important; width: {{$largura}}mm !important; display: inline-block; margin-top: {{$distancia_topo}}mm !important; margin-left: {{$quantidade_por_linhas > 1 && $contLinha > 0 ? $distancia_lateral : 4 }}mm !important;" class="sticker-border text-center">

		<div style="display:inline-block;vertical-align:middle;line-height:10px !important; margin-top: {{$distancia_topo}}mm;">

			@if($item['nome_empresa'])
			<b style="display: block !important; font-size: {{$tamanho_fonte}}px" class="text-uppercase">{{$item['empresa']}}</b>
			@endif

			@if($item['nome_produto'])
			<span style="display: block !important; font-size: {{$tamanho_fonte}}px; margin-top: {{$distancia_entre_linhas}}px;">
				{{$item['nome']}}
			</span>
			@endif
			@if($item['cod_produto'])
			<span style="display: block !important; margin-top: {{$distancia_entre_linhas}}px; font-size: {{$tamanho_fonte}}px">
				ID: <b>{{$item['codigo']}}</b>
			</span>
			@endif
			<img class="center-block" style="max-width:90%; !important;height: {{$tamanho_codigo}}mm !important;" src="/barcode/{{$item['rand']}}.png">
			@if($item['codigo_barras_numerico'])
			<span style="display: block !important; font-size: {{$tamanho_fonte}}px;">{{$item['codigo']}}</span>
			@endif
			
			@if($item['valor_produto'])
			<span style="display: block !important; font-size: {{$tamanho_fonte}}px; margin-top: {{$distancia_entre_linhas}}px;">
				<b>R$ {{number_format($item['valor'], 2, ',', '.')}}</b>
			</span>
			@endif
			@if($valor_atacado)
			<span style="display: block !important; font-size: {{$tamanho_fonte}}px; margin-top: {{$distancia_entre_linhas}}px;">
				<b>R$ {{number_format($item['valor_atacado'], 2, ',', '.')}}</b>
			</span>
			@endif

			@if($referencia)
			<span style="display: block !important; font-size: {{$tamanho_fonte}}px; margin-top: {{$distancia_entre_linhas}}px;">
				<b>REF:{{ $item['referencia'] }}</b>
			</span>
			@endif
		</div>
	</div>

	@php
	$contLinha++;
	if($contLinha == $quantidade_por_linhas){
	echo "<br>"; $contLinha = 0;
}
@endphp
@endforeach
</div>

<script type="text/javascript">

</script>

<style type="text/css">

	.text-center{
		text-align: center;
	}

	.text-uppercase{
		text-transform: uppercase;
	}

	/*Css related to printing of barcode*/
	.label-border-outer{
		border: 0.1px solid grey !important;
	}
	.label-border-internal{
		/*border: 0.1px dotted grey !important;*/
	}
	.sticker-border{
		border: 0.1px dotted grey !important;
		overflow: hidden;
		box-sizing: border-box;
	}
	#preview_box{
		padding-left: 30px !important;
	}
	@media print{
		.content-wrapper{
			border-left: none !important; /*fix border issue on invoice*/
		}
		.label-border-outer{
			border: none !important;
		}
		.label-border-internal{
			border: none !important;
		}
		.sticker-border{
			border: none !important;
		}
		#preview_box{
			padding-left: 0px !important;
		}
		#toast-container{
			display: none !important;
		}
		.tooltip{
			display: none !important;
		}
		.btn{
			display: none !important;
		}
	}

	@media print{
		#preview_body{
			display: block !important;
		}
	}
	@page {
		margin-top: 0in;
		margin-bottom: 0in;
		margin-left: 0in;
		margin-right: 0in;

	}
</style>