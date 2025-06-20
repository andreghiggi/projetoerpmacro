<div class="minfit" style="min-height: 472px;">
	<div class="middle">
		<div class="container nopaddmobile">
			<div class="row rowtitle">
				<div class="col-md-12">
					<div class="title-icon">
						<span>Fechar Pedido</span>
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
			<div class="row">
				<button class="js-push-btn botao-acao" style="display: none;">Ativar notificações</button>
			</div>
			<div class="sacola">
				<form id="the_form" method="POST" action="" novalidate="novalidate">
					<div class="row">
						<div class="col-md-12">
							<table class="listing-table sacola-table">
								<thead>
									<tr><th></th>
										<th>Nome</th>
										<th>Qtd</th>
										<th>Valor</th>
										<th>SubTotal</th>
										<th>Detalhes</th>
									</tr>
								</thead>
								<tbody>
									@foreach($carrinho->itens as $i)
									<tr class="sacola-alterar sacola-{{ $i->id }}" sacola-produto-id="{{ $i->produto_id }}" sacola-eid="{{ $i->id }}" sacola-pid="{{ $i->produto_id }}" valor-adicional="0" valor-somado="{{ $i->sub_total 	}}">
										<td class="td-foto">
											<a href="{{ route('food.produto-detalhe', [$i->produto->hash_delivery]) }}">
												<div class="imagem">
													<img src="{{ $i->produto->img }}">
												</div>
											</a>
										</td>
										<td class="td-nome">
											<a href="">
												<span class="nome">{{ $i->produto->nome }}</span>
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
													<a class="sacola-change" style="display:none" href="{{ route('food.produto-detalhe', [$i->produto->hash_delivery]) }}">
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
										
									</tr>
									@endforeach

									@if(sizeof($carrinho->itens) == 0)
									<tr class="sacola-null">
										<td colspan="6"><span class="nulled">Sua sacola de compras está vazia, adicione produtos para poder fazer o seu pedido!</span></td>
									</tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
					<div class="linha-subtotal">
						<div class="row error-pedido-minimo error-pedido-minimo-sacola">
							<div class="col-md-12">
								<input class="fake-hidden" name="pedido_minimo" value="{{ $config->pedido_minimo }}">
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="subtotal"><strong>Subtotal:</strong> <span class="subtotal-valor">R$ {{ __moeda($carrinho->valor_total) }}</span></div>
							</div>
							<div class="clear visible-xs visible-sm"><br></div>
							<div class="col-md-3">
								<form id="the_form" method="POST">
									<input class="fake-hidden" name="pedido_minimo" value="">
									<button class="botao-acao"><i class="lni lni-bi-cycle"></i> <span>Delivery</span></button>
								</form>
							</div>
							<div class="clear visible-xs visible-sm"><br></div>
							<div class="col-md-3">
								<form id="the_form" method="POST">
									<input class="fake-hidden" name="pedido_minimo" value="">
									<button class="botao-acao"><i class="lni lni-restaurant"></i> <span>Retirada Balcão</span></button>
								</form>
							</div>
							<div class="clear visible-xs visible-sm"><br></div>
						</div>
					</div>

				</form>

			</div>
		</div>
	</div>
</div>