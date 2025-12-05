@extends('food.default', ['title' => 'Carrinho'])

@section('css')

<link rel="stylesheet" href="/food-files/css/extend_cart.css">

@endsection
@section('content')

<div class="minfit" style="min-height: 472px;">
	<div class="middle">
		<div class="container nopaddmobile">
			<div class="row rowtitle">
				<div class="col-md-12">
					<div class="title-icon">
						@if(__isProdutoServicoDelivery($config->empresa_id))
						<span>Finalizar Pedido/Agendamento</span>
						@else
						@if(__isSegmentoServico($config->empresa_id))
						<span>Finalizar Agendamento</span>
						@else
						<span>Finalizar Pedido</span>
						@endif
						@endif
					</div>
					<div class="bread-box">
						<div class="bread">
							<a href="{{ route('food.index', ['link='.$config->loja_id]) }}"><i class="lni lni-home"></i></a>
							<span>/</span>
							<a href="{{ route('food.carrinho', ['link='.$config->loja_id]) }}">Meu carrinho</a>
						</div>
					</div>
				</div>
				<div class="col-md-12 hidden-xs hidden-sm">
					<div class="clearline"></div>
				</div>
			</div>
			<div class="sacola">
				<div id="the_form" novalidate="novalidate">
					<div class="row">
						<div class="col-md-12">
							<table class="listing-table sacola-table">
								<thead>
									<tr>
										<th></th>
										<th>Nome</th>
										<th>Qtd</th>
										<th>Valor</th>
										<th>SubTotal</th>
										<th>Detalhes</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@if($carrinho)
									@foreach($carrinho->itens as $i)
									<tr class="sacola-alterar sacola-{{ $i->id }}" sacola-produto-id="{{ $i->produto_id }}" sacola-eid="{{ $i->id }}" sacola-pid="{{ $i->produto_id }}" valor-adicional="0" valor-somado="{{ $i->sub_total 	}}">
										<td class="td-foto">
											@if($i->produto)
											<a href="{{ route('food.produto-detalhe', [$i->produto->hash_delivery]) }}">
												<div class="imagem">
													<img src="{{ $i->produto->img }}">
												</div>
											</a>
											@else
											<a href="{{ route('food.servico-detalhe', [$i->servico->hash_delivery]) }}">
												<div class="imagem">
													<img src="{{ $i->servico->img }}">
												</div>
											</a>
											@endif
										</td>
										<td class="td-nome">
											<a href="">
												<span class="nome">
													@if($i->tamanho)
													@foreach($i->pizzas as $pizza)
													1/{{ sizeof($i->pizzas) }} {{ $pizza->sabor->nome }} @if(!$loop->last) | @endif
													@endforeach
													- Tamanho: <strong>{{ $i->tamanho->nome }}</strong>
													@else
													{{ $i->produto ? $i->produto->nome : $i->servico->nome }}
													@endif
												</span>
											</a>
										</td>
										<td class="td-detalhes visible-xs visible-sm">
											<div class="line detalhes">
												<span>
													{{ $i->observacao }}
												</span>
											</div>
										</td>
										<td class="td-quantidade">
											<div class="clear"></div>
											<div class="holder-acao">
												<div class="item-acao visible-xs visible-sm">
													@if($i->produto)
													<a class="sacola-change" style="display:none" href="{{ route('food.produto-detalhe', [$i->produto->hash_delivery]) }}">
														<i class="lni lni-pencil"></i>
													</a>
													@else
													<a class="sacola-change" style="display:none" href="{{ route('food.servico-detalhe', [$i->servico->hash_delivery]) }}">
														<i class="lni lni-pencil"></i>
													</a>
													@endif
												</div>
												<div class="item-acao">
													<div class="line quantidade">
														<div class="clear"></div>
														<div class="campo-numero">
															<i class="decrementar lni lni-minus" sacola-eid="{{ $i->id }}"></i>
															<input readonly="" id="quantidade" type="number" name="quantidade" value="{{ number_format($i->quantidade, 0) }}">
															<i class="incrementar lni lni-plus" sacola-eid="{{ $i->id }}"></i>
														</div>
														<div class="clear"></div>
													</div>
												</div>
												<div class="item-acao visible-xs visible-sm">
													<a class="sacola-remover" href="#" sacola-eid="{{ $i->id }}" sacola-tipo="{{ $i->produto ? 'produto' : 'servico' }}">
														<i class="lni lni-trash"></i>
													</a>
												</div>
											</div>
										</td>
										<td class="td-valor">
											<span>Valor:</span>
											<div class="line valor">
												<span>R$ {{ __moeda($i->valor_unitario) }}</span>
											</div>
										</td>
										<td class="td-valor">
											<span>Sub total:</span>
											<div class="line valor">
												<span class="sub_total_item">R$ {{ __moeda($i->sub_total) }}</span>
											</div>
										</td>
										<td class="td-detalhes hidden-xs hidden-sm">
											<div class="line detalhes">
												<span>
													{{ $i->observacao }}
												</span>
											</div>
										</td>
										<td class="td-acoes hidden-xs hidden-sm">
											<div class="holder">
												<a class="sacola-remover" href="#" sacola-eid="{{ $i->id }}" sacola-tipo="{{ $i->produto ? 'produto' : 'servico' }}">
													<i class="lni lni-trash"></i>
													<span class="visible-xs">Excluir</span>
												</a>
											</div>
											<div class="clear visible-xs visible-sm"></div>
										</td>
									</tr>
									@if(sizeof($i->adicionais) > 0)
									<tr class="sacola-{{ $i->id }}">
										<td colspan="7">
											Adicionais: 
											@foreach($i->adicionais as $a)
											<strong>{{ $a->adicional->nome }}@if(!$loop->last), @endif</strong>
											@endforeach
										</td>
									</tr>
									@endif
									@endforeach
									@else
									<tr class="sacola-null">
										<td colspan="6"><span class="nulled">Sua sacola de compras está vazia, adicione produtos para poder fazer o seu pedido!</span></td>
									</tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
					<div class="linha-subtotal">
						@if($carrinho)
						<div class="row error-pedido-minimo error-pedido-minimo-sacola">
							<div class="col-md-12">
								<input class="fake-hidden" name="pedido_minimo" value="{{ $config->pedido_minimo }}">
								<input class="fake-hidden" id="inp-carrinho_id" value="{{ $carrinho->id }}">
							</div>
						</div>
						@endif

						@if($funcionamento && $funcionamento->aberto)
						@if($carrinho && $carrinho->valor_total >= $config->pedido_minimo)
						<div class="row">

							<div class="col-md-4">
								<a class="botao-continuar" href="{{ route('food.index', ['link='.$config->loja_id]) }}">
									<i class="lni lni-cart"></i> <span>Continuar Comprando</span>
								</a>
								<div class="subtotal"><strong>Subtotal:</strong> <span class="subtotal-valor">R$ {{ $carrinho ? __moeda($carrinho->valor_total) : '0,00' }}</span></div>
								@if($tempoServico > 0)
								<div><strong>Tempo de serviço:</strong> {{ $tempoServico }} Minutos</div>
								@endif

								
							</div>
							<div class="clear visible-xs visible-sm"><br></div>

							@if($temServico == 0)
							@if(in_array('delivery', $config->tipo_entrega))
							<div class="col-md-3">
								<form method="get" action="{{ route('food.pagamento') }}">

									<input class="fake-hidden" name="delivery" value="1">
									<input type="hidden" name="link" value="{{ $config->loja_id }}" />

									<button class="botao-acao"><i class="lni lni-bi-cycle"></i> <span>Delivery</span></button>
								</form>
							</div>
							@endif

							<div class="clear visible-xs visible-sm"><br></div>
							@if(in_array('balcao', $config->tipo_entrega))
							<div class="col-md-3">
								<form method="get" action="{{ route('food.pagamento') }}">
									<input class="fake-hidden" name="balcao" value="1">
									<input type="hidden" name="link" value="{{ $config->loja_id }}" />

									<button class="botao-acao"><i class="lni lni-restaurant"></i> <span>Retirada Balcão</span></button>
								</form>
							</div>
							@endif
							@endif

							<div class="clear visible-xs visible-sm"><br></div>
						</div>
						@else

						<h5 class="text-primary">Pedido mínimo R$ {{ __moeda($config->pedido_minimo) }}</h5>

						@endif
						@else
						<div class="loja-fechada">
							<span><button>Fechado</button></span>
						</div>
						@endif

						@if($temServico == 1)
						<div class="row">
							<br>
							<div class="col-md-3">
								<div class="form-field-default">
									<label>Data do agendamento:</label>
									<input class="" type="date" id="inp-data_agendamento" value="">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-field-default">
									<br>
									<button type="button" id="btn-buscar-horarios" class="botao-acao-gray active" style="width: 100%; margin-top: 6px" type="button">
										<i class="lni lni-search-alt"></i>
										Buscar horários
									</button>

								</div>
							</div>
						</div>
						@endif

						<hr>
						<div class="row atendentes">


						</div>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>

@include('food.partials.modal_escolhe_agendamento')

@endsection
@section('js')
<script src="/food-files/js/carrinho.js"></script>
<script src="/food-files/js/agendamento.js"></script>
@endsection
