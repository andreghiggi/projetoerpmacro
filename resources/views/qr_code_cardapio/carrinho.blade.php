@extends('qr_code_cardapio.default', ['title' => 'Carrinho'])
@section('content')

<div class="minfit" style="min-height: 472px;">
	<div class="middle">
		<div class="container nopaddmobile">
			<div class="row rowtitle">
				<div class="col-md-12">
					<div class="title-icon">
						<span>Carrinho</span>

					</div>
					<div class="bread-box">
						<div class="bread">
							<a href="{{ route('cardapio.index', ['link='.$link]) }}"><i class="lni lni-home"></i></a>
							<span>/</span>
							<a href="{{ route('cardapio.carrinho', ['link='.$link]) }}">Meu carrinho <strong style="color: var(--main) !important">{{ $mesa->nome }}</strong></a>
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
									@if($carrinho && sizeof($carrinho->itens) >= 0)
									@foreach($carrinho->itens as $i)
									<tr class="sacola-alterar sacola-{{ $i->id }}" sacola-produto-id="{{ $i->produto_id }}" sacola-eid="{{ $i->id }}" sacola-pid="{{ $i->produto_id }}" valor-adicional="0" valor-somado="{{ $i->sub_total 	}}">
										<td class="td-foto">

											<a href="{{ route('cardapio.produto-detalhe', [$i->produto->hash_delivery, 'link='.$link]) }}">
												<div class="imagem">
													<img src="{{ $i->produto->img }}">
												</div>
											</a>
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

													<a class="sacola-change" style="display:none" href="{{ route('cardapio.produto-detalhe', [$i->produto->hash_delivery]) }}">
														<i class="lni lni-pencil"></i>
													</a>
													
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
													<a class="sacola-remover" href="#" sacola-eid="{{ $i->id }}">
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
										<td colspan="5">
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


						@if($carrinho && $carrinho->valor_total > 0 && $travaLimite == 0)
						<div class="row">
							<form method="post" action="{{ route('cardapio.enviar-pedido') }}">

								@if($pedido == null)

								<div class="col-md-12">
									<p class="text-danger">Preencha nome e telefone para adicionar o pedido</p>
								</div>
								<div class="col-md-4" style="margin-top: 10px;">
									<input type="text" name="nome" value="" required placeholder="Nome:">
								</div>
								<div class="col-md-4" style="margin-top: 10px;">
									<input class="maskcel" type="text" name="telefone" placeholder="Telefone/Whatsapp:" required>
								</div>
								@endif
								<div class="col-md-9">
									<div class="subtotal"><strong>Subtotal:</strong> <span class="subtotal-valor">R$ {{ $carrinho ? __moeda($carrinho->valor_total) : '0,00' }}</span></div>

								</div>
								<div class="clear visible-xs visible-sm"><br></div>


								<div class="col-md-3">
									@csrf
									<input class="fake-hidden" name="balcao" value="1">
									<input type="hidden" name="link" value="{{ $link }}" />

									<button @if($pedido != null && !$pedido->confirma_mesa) disabled @endif class="botao-acao"><span>Enviar Pedido</span></button>
								</div>
							</form>

							@if($pedido != null && !$pedido->confirma_mesa)
							<div class="col-md-12">
								<p class="text-danger">Aguarde a mesa ser liberada para pedir</p>
							</div>
							@endif

							<div class="clear visible-xs visible-sm"><br></div>
						</div>
						@else

						@endif


						@if($travaLimite)
						<div class="col-md-12">
							<p class="text-danger">Limite de clientes por mesa atingido!</p>
						</div>
						@endif
						<hr>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>


@endsection
@section('js')
<script src="/food-files/js/carrinho_cardapio.js"></script>
@endsection
