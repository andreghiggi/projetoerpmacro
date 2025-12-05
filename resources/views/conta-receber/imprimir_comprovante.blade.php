<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		.footer {
			position: fixed;
			bottom: 0px;
			padding: 0;
		}
		.footer small{
			color:grey; 
			font-size: 10px;
			text-align: left;
			margin-top: 100px !important;
		}
		body{
			width: 260px;
			/*background: #000;*/
			margin-left: -40px;
			margin-top: -40px;
		}
		.mt-20{
			margin-top: -20px;
		}
		h5 strong{
			color: #49526B;
		}
		.mt-10{
			margin-top: -10px;
		}
		.mt-45{
			margin-top: -50px;
		}
		.mt-25{
			margin-top: -25px;
		}
		table th{
			font-size: 10px;
			text-align: left;
		}

		table td{
			font-size: 11px;
		}

	</style>
</head>
<header>
	<div class="headReport">
	</div>
</header>
<body>
	<h5 style="text-align:center; " class="mt-10">COMPROVANTE DE PAGAMENTO</h5>
	<h5>Cliente: <strong>{{ $item->cliente->info }}</strong></h5>
	<h5 class="mt-20">Valor Integral: <strong>R$ {{ __moeda($item->valor_original > 0 ? $item->valor_original : $item->valor_integral) }}</strong></h5>
	<h5 class="mt-20">Valor Recebido: <strong>R$ {{ __moeda($item->valor_recebido) }}</strong></h5>
	<h5 class="mt-20">Data do vencimento: <strong>{{ __data_pt($item->data_vencimento, 0) }}</strong></h5>
	<h5 class="mt-20">Data do recebimento: <strong>{{ __data_pt($item->data_recebimento, 0) }}</strong></h5>
	<h5 class="mt-20">Data de cadastro: <strong>{{ __data_pt($item->created_at) }}</strong></h5>


</body>
