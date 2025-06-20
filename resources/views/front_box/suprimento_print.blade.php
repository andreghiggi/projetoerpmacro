<h6 style="margin-left: 0px; text-align: center; margin-top: -30px;">
	{{ $empresa->nome }}
	{{ $empresa->rua }}, {{ $empresa->numero}}
	{{ $empresa->bairro }}, {{ $empresa->cep}}<br>
	{{ $empresa->cidade->info }}

</h6>

<h5 style="font-size: 12px; text-align: center;">COMPROVANTE DE SUPRIMENTO</h5>

<h5 style="margin-top: -10px;text-align: center;">R$ {{ __moeda($suprimento->valor) }}</h5>
<h5 style="margin-top: -25px;text-align: center; font-size: 8px">{{ __data_pt($suprimento->created_at) }}</h5>
<h5 style="margin-top: -15px;text-align: center; font-size: 8px">Tipo de pagamento: {{ \App\Models\Nfce::getTipoPagamento($suprimento->tipo_pagamento) }}</h5>

@if($suprimento->observacao)
<h5 style="margin-top: -15px;text-align: center; font-size: 8px">Observação: {{ $suprimento->observacao }}</h5>
@endif

<h5 style="margin-top: 20px;text-align: center; font-size: 10px">
	________________________________________
</h5>
<h5 style="margin-top: -15px;text-align: center; font-size: 10px">
	Assinatura
</h5>