<style type="text/css">
	.main{
		margin-top: -20px;
		font-family: Verdana, Arial, Helvetica, sans-serif;
	}
	.bold{
		font-weight: bold;
	}
	
</style>
<div class="main">
	<h3 class="bold">{{ $config->nome }}</h3>
	<h4>{{ $config->rua }}, {{ $config->numero }}<br>{{ $config->bairro }}<br>{{ $config->cidade->info }}</h4>
	<label style="margin-top: -10px">________________________________</label>
	<h4 class="bold">Pedido</h4>
	<label style="margin-top: -10px">________________________________</label>
	<h4>{{ date('d/m/Y H:i') }}</h4>
	<label style="margin-top: -10px">________________________________</label>

	<h4 class="bold">Nome<br><span style="font-weight: 100;">{{ $carrinho->cliente->razao_social }}</span></h4>
	<h4 class="bold">WhatsApp<br><span style="font-weight: 100;">{{ $carrinho->fone }}</span></h4>
	<h4 class="bold">Endereço<br>
		@if($carrinho->endereco)
		<span style="font-weight: 100;">{{ $carrinho->endereco->info }}
		</span>
		@endif
	</h4>
	<h4 class="bold">Forma de pagamento<br>
		<span style="font-weight: 100;">
			{{ $carrinho->tipo_pagamento }}
			@if($carrinho->troco_para > 0)
			- troco para: R$ {{ __moeda($carrinho->troco_para) }}
			@endif
		</span>
	</h4>

	<label style="margin-top: -10px">________________________________</label>

	@if(sizeof($carrinho->itensProdutos) > 0)
	<h4 class="bold">Produtos</h4>

	@foreach($carrinho->itens as $i)
	<h5>
		@if($i->produto)
		<strong>{{ number_format($i->quantidade, 0) }} X</strong>
		@if($i->produto->referencia)
		#REF-{{ $i->produto->referencia }}
		@endif
		@if($i->tamanho)
		@foreach($i->pizzas as $pizza)
		1/{{ sizeof($i->pizzas) }} {{ $pizza->sabor->nome }} @if(!$loop->last) | @endif
		@endforeach
		- tamanho: <strong>{{ $i->tamanho->nome }}</strong>
		@else
		{{ $i->produto->nome }}
		@endif
		@if(sizeof($i->adicionais) > 0)
		<br>
		adicionais: 
		@foreach($i->adicionais as $a)
		<strong>{{ $a->adicional->nome }}@if(!$loop->last), @endif</strong>
		@endforeach
		@endif
		<br><strong>Valor:</strong>R$ {{ __moeda($i->sub_total) }}
		@endif
	</h5>
	@endforeach
	@endif

	@if(sizeof($carrinho->itensServico) > 0)
	<h4 class="bold">Serviços</h4>

	@foreach($carrinho->itens as $i)
	<h5>
		@if($i->servico)
		<strong>{{ number_format($i->quantidade, 0) }} X</strong>
		{{ $i->servico->nome }}
		<br><strong>Valor:</strong>R$ {{ __moeda($i->sub_total) }}
		@endif
	</h5>
	@endforeach
	@endif

	<label style="margin-top: -10px">________________________________</label>
	<h4><strong>Subtotal:</strong> R$ {{ __moeda($carrinho->itens->sum('sub_total')) }}</h4>

	@if($carrinho->observacao)
	<h4><strong>Observações:</strong> {{ $carrinho->observacao }}</h4>
	@endif
	@if($carrinho->endereco && $carrinho->endereco->bairro)
	<h4><strong>Entrega:</strong> {{ $carrinho->endereco->bairro->nome }} +(R$ {{ __moeda($carrinho->valor_frete) }})</h4>
	@endif

	@if($carrinho->tipo_entrega == 'balcao')
	<h4><strong>Retirada em balcão</strong></h4>
	@endif

	@if($carrinho->valor_desconto > 0)
	<h4><strong>Desconto:</strong> -R$ {{ __moeda($carrinho->valor_desconto) }}</h4>
	@endif
	<h4><strong>Total:</strong> R$ {{ __moeda($carrinho->valor_total) }}</h4>


</div>