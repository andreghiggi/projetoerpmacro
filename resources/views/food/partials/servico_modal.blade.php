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
							<a href="{{ route('food.index', ['link='.$config->loja_id]) }}"><i class="lni lni-home"></i></a>
							@if($item->categoria)
							<span>/</span>
							<a>Categorias</a>
							<span>/</span>
							<a>{{ $item->categoria->nome }}</a>
							@endif
						</div>
					</div>
				</div>
				<input type="hidden" id="valor_unitario_produto" value="{{ $item->valor }}">
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

								<div class="row">
									<div class="col-md-3 hidden-xs hidden-sm">
										<span class="thelabel thelabel-normal">Link do Serviço:</span>
									</div>
									<div class="col-md-9">
										<div class="row">
											<div class="col-md-12">
												<div class="ref">
													<span><a href="https://api.whatsapp.com/send?text={{ route('food.servico-detalhe', [$item->hash_delivery, 'link='.$config->loja_id]) }}" target="_blank" class="btn btn-success btn-sm" style="color:#FFFFFF; margin-top: 3px"><i class="lni lni-whatsapp"></i> Compartilhe no WhatsApp</a></span>
												</div>
											</div>
										</div>
									</div>
								</div>


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
													<span>R$ {{ __moeda($item->valor) }}</span>
												</div>
											</div>
										</div>
									</div>
								</div>

							</div>

							<form id="the_form" method="POST" action="{{ route('food.adicionar-carrinho-servico') }}">

								<input type="hidden" name="_token" id="_token_api">
								<input type="hidden" value="{{ $config->loja_id }}" name="link">
								<input type="hidden" value="{{ $item->valor }}" name="sub_total" id="inp-sub_total">
								<input type="hidden" value="{{ $item->id }}" name="servico_id" id="servico_id">

								<div class="sabores-input"></div>
								
								<div class="comprar">


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

											<div class="col-md-6" style="margin-bottom:20px">
												<div class="subtotal"><strong>Total:</strong> R$ {{ __moeda($item->valor) }}</div>
											</div>
											
											<div class="col-md-6">
												<button class="sacola-adicionar botao-acao pull-right"><i class="icone icone-sacola"></i> <span>Adicionar serviço</span></button>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<button type="button" style="margin-top:20px; color:white" class="btn" data-dismiss="modal">Cancelar</button>
											</div>
										</div>
									</div>
									
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