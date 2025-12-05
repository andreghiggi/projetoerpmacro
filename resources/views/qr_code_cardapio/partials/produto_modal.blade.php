<div class="sceneElement">
	<div class="middle">
		<div class="nopaddmobile">
			<div class="row rowtitle hidden-xs hidden-sm">
				<div class="col-md-12">
					<div class="title-icon">
						<span>{{ $item->nome }}</span>
					</div>
					<div class="bread-box">
						<div class="bread">
							<a href="{{ route('cardapio.index', ['link='.$link]) }}"><i class="lni lni-home"></i></a>
							@if($item->categoria && $item->categoria->hash_delivery)
							<span>/</span>
							<a href="{{ route('cardapio.produtos-categoria', [$item->categoria->hash_delivery, 'link='.$link]) }}">Categorias</a>
							<span>/</span>
							<a href="{{ route('cardapio.produtos-categoria', [$item->categoria->hash_delivery, 'link='.$link]) }}">{{ $item->categoria->nome }}</a>
							@endif
						</div>
					</div>
				</div>
				<input type="hidden" id="valor_unitario_produto" value="{{ $item->valor_cardapio }}">
				<input type="hidden" id="valor_unitario_pizza" value="">
				
				<div class="col-md-12 hidden-xs hidden-sm">
					<div class="clearline"></div>
				</div>
			</div>

			<div class="single-produto nost" style="margin-top:-28px">

				<div class="row">
					<div class="col-md-5">
						<div class="galeria">
							<div class="row">
								<div class="col-md-12">
									<div id="carouselgaleria" class="carousel slide">
										<div class="carousel-inner">
											<div class="item zoomer active">
												<a data-fancybox="galeria" href="{{ $item->img }}">
													<img src="{{ $item->img }}" alt="{{ $item->nome }}" title="{{ $item->nome }}">
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-7">
						<div class="padd-container-mobile">
							<div class="produto-detalhes">
								<div class="row visible-xs visible-sm">
									<div class="col-md-12">
										<div class="nome">
											<span>{{ $item->nome }}</span>
										</div>
									</div>
								</div>

								@if($item->referencia)
								<div class="row">
									<div class="col-md-3 hidden-xs hidden-sm">
										<span class="thelabel thelabel-normal">REF:</span>
									</div>

									<div class="col-md-9">
										<div class="row">
											<div class="col-md-12">
												<div class="ref">
													<span>#REF-{{ $item->referencia }}</span>
												</div>
											</div>
										</div>

									</div>
								</div>
								@endif

								@if(!$tipoPizza)
								<div class="row">
									<div class="col-md-3 hidden-xs hidden-sm">
										<span class="thelabel thelabel-normal">Detalhes:</span>
									</div>
									<div class="col-md-9">
										<div class="row">
											<div class="col-md-12">												
												<div class="valor_anterior">
													<span> Apenas</span>
												</div>

												<div class="valor">
													<span>R$ {{ __moeda($item->valor_cardapio) }}</span>
												</div>
											</div>
										</div>
									</div>
								</div>
								@endif

							</div>

							<form id="the_form" class="form-modal-item" method="POST" action="{{ route('cardapio.adicionar-carrinho') }}">

								<input type="hidden" name="_token" id="_token_api">
								<input type="hidden" value="{{ $link }}" name="link">
								<input type="hidden" value="{{ $item->valor_cardapio }}" name="sub_total" id="inp-sub_total">
								<input type="hidden" value="{{ $item->id }}" name="produto_id" id="produto_id">
								<div class="sabores-input"></div>
								
								<div class="comprar">

									@if(sizeof($item->adicionaisAtivos) > 0)
									<div class="line line-variacao">
										<div class="row">
											<div class="col-md-6">
												<span class="thelabel pull-left">
													ADICIONAIS														
												</span>
											</div>
										</div>
										<div class="row">
											<div class="opcoes-input"></div>

											@foreach($categoriasAdicional as $c)
											<div class="adicional_categoria_{{ $c->id }}">
												<input type="hidden" value="{{ $c->minimo_escolha }}" class="minimo_escolha">
												<input type="hidden" value="{{ $c->nome }}" class="categoria_nome">
												<input type="hidden" value="{{ $c->minimo_escolha == 0 ? 1 : 0 }}" class="minimo_escolha_valida">

												<input type="hidden" value="{{ $c->maximo_escolha }}" class="maximo_escolha">
												<input type="hidden" value="0" class="maximo_escolha_contador">
												<h5 style="margin-left: 15px;">
													{{ $c->nome }}
													@if($c->minimo_escolha > 0)
													<strong> (selecione ao menos {{ $c->minimo_escolha }})</strong>
													@endif
												</h5>
												<div class="col-md-12">

													<div class="opcoes">

														@foreach($c->adicionais as $a)
														@if(in_array($a->id, $item->adicionaisAtivos->pluck(['adicional_id'])->toArray()))

														<div class="opcao op_{{ $a->id }}" categoria-adicional-id="{{ $c->id }}" adicional-id="{{ $a->id }}" adicional-valor="{{ $a->valor }}">
															<div class="check" style="height: 62px;">
																<i class="lni"></i>
															</div>
															<div class="detalhes">
																<span class="titulo" style="font-size:12px"> 
																	{{ $a->nome }} 
																	@if($a->valor > 0)
																	R$ {{ __moeda($a->valor) }}
																	@else
																	Gratis
																	@endif
																</span>
																<span class="descricao" style="font-size:10px"></span>
															</div>

															<div class="clear"></div>
														</div>
														@endif
														@endforeach
													</div>
												</div>
											</div>
											@endforeach

										</div>
									</div>
									@endif

									@if($tipoPizza)
									<div class="row" style="margin-top: 10px;">
										<div class="col-md-8">
											<label>Tamanho da pizza</label>
											<div class="fake-select">
												<i class="lni lni-chevron-down"></i>
												<select id="tamanho_id" name="tamanho_id">
													<option value="">Selecione o tamanho</option>
													@foreach($tamanhosPizza as $t)
													<option value="{{ $t->id }}">
														{{ $t->nome }} - {{ $t->quantidade_pedacos }} fatias, até {{ $t->maximo_sabores }} sabores
													</option>
													@endforeach
												</select>
												<div class="clear"></div>
											</div>
										</div>

										<div class="col-md-12" style="margin-top: 15px">
											<label>Deseja adicionar outro sabor para sua pizza?</label>
											<input type="text" id="inp-pesquisa_sabor" placeholder="Pesquise o sabor">
										</div>

										<div class="col-md-12 div-sabores" style="margin-top: 10px;">
										</div>

										<div class="col-md-12 div-sabores-selecionados" style="margin-top: 10px;">
										</div>
									</div>
									@endif

									<div class="line quantidade">
										<div class="row">
											<div class="col-md-3 col-sm-2 col-xs-2">
												<span class="thelabel hidden-xs hidden-sm">Quantidade:</span>
												<span class="thelabel visible-xs visible-sm">Qtd:</span>
											</div>
											<div class="col-md-9 col-sm-10 col-xs-10">
												<div class="campo-numero pull-right">
													<i class="decrementar lni lni-minus"></i>
													<input id="quantidade" readonly type="number" name="quantidade" value="1">
													<i class="incrementar lni lni-plus"></i>
												</div>
											</div>
										</div>
									</div>
									@if($item->descricao)
									<div class="line observacoes">
										<div class="row">
											<div class="col-md-3 col-sm-2 col-xs-2">
												<span class="thelabel hidden-xs hidden-sm">Descrição:</span>
												<span class="thelabel visible-xs visible-sm">Obs:</span>
											</div>
											<div class="col-md-9 col-sm-10 col-xs-10">
												<p>{{ $item->descricao }}</p>
											</div>
										</div>
									</div>
									@endif
									<div class="line observacoes">
										<div class="row">
											<div class="col-md-3 col-sm-2 col-xs-2">
												<span class="thelabel hidden-xs hidden-sm">Observações:</span>
												<span class="thelabel visible-xs visible-sm">Obs:</span>
											</div>
											<div class="col-md-9 col-sm-10 col-xs-10">
												<textarea id="observacoes" rows="5" name="observacao" placeholder="Observações:"></textarea>
											</div>
										</div>
									</div>
									<div id="botoes_fim" class="line botoes">
										<div class="row">
											@if(!$tipoPizza)
											<div class="col-md-6" style="margin-bottom:20px">
												<div class="subtotal"><strong>Total:</strong> R$ {{ __moeda($item->valor_cardapio) }}</div>
											</div>
											@else
											<div class="col-md-6" style="margin-bottom:20px">
												<div class="subtotal"><strong>Total:</strong><span class="total-pizza">R$ {{ __moeda(0) }}</span></div>
											</div>
											@endif
											<div class="col-md-6">
												<button type="button" @if($tipoPizza) disabled @endif class="sacola-adicionar botao-acao pull-right"><i class="icone icone-sacola"></i> <span>Adicionar na sacola</span></button>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<button type="button" style="margin-top:20px; color:white" class="btn" data-dismiss="modal">Cancelar</button>
											</div>
										</div>
									</div>
									@if(sizeof($item->ingredientes) > 0)
									<div class="product__details__tab__desc">
										<h5 style="font-weight: bold">Ingredientes</h5>
										<p>@foreach($item->ingredientes as $i) {{ $i->ingrediente }}{{ !$loop->last ? ', ' : '' }} @endforeach</p>
									</div>
									@endif
								</div>
							</form>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="fillrelacionados visible-xs visible-sm"></div>
</div>