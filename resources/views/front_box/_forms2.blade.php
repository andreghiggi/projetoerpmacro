@section('css')
<link rel="stylesheet" type="text/css" href="/css/frontbox2.css">
@endsection

<input type="hidden" id="inp-valor_total">
<input type="hidden" id="inp-valor_total2" name="valor_total">
<input type="hidden" id="abertura" value="{{ $abertura }}" name="">
<input type="hidden" id="tef_hash" value="" name="tef_hash">
<input type="hidden" id="config_tef" value="{{ isset($configTef) && $configTef != null ? 1 : 0 }}">
<input type="hidden" id="agrupar_itens" value="{{ $config ? $config->agrupar_itens : 0 }}" name="">
<input type="hidden" id="venda_id" value="{{ isset($item) ? $item->id : '' }}">
<input type="hidden" id="lista_id" value="" name="lista_id">
<input type="hidden" id="alerta_sonoro" value="{{ $config ? $config->alerta_sonoro : 0 }}">
<input type="hidden" id="impressao_sem_janela_cupom" value="{{ $config ? $config->impressao_sem_janela_cupom : 0 }}">
<input type="hidden" id="local_id" value="{{ $caixa->localizacao->id }}">
<input type="hidden" id="documento_pdv" value="{{ $config ? $config->documento_pdv : 'nfce' }}">

@if($isVendaSuspensa)
<input type="hidden" value="{{ $item->id }}" name="venda_suspensa_id">
@endif

@isset($pedido)
@isset($isDelivery)
<input name="pedido_delivery_id" id="pedido_delivery_id" value="{{ $pedido->id }}" class="d-none">
<input id="pedido_desconto" value="{{ $pedido->desconto ? $pedido->desconto : 0 }}" class="d-none">
<input name="valor_entrega" id="pedido_valor_entrega" value="{{ $pedido->valor_entrega }}" class="d-none">
@else
<input name="pedido_id" id="pedido_id" value="{{ $pedido->id }}" class="d-none">
@isset($pushItensPedido)
<input name="itens_cliente" id="pedido_id" value="{{ json_encode($pushItensPedido) }}" class="d-none">
@endif
@endif
@endif

@if(isset($config))
<input type="hidden" id="inp-abrir_modal_cartao" value="{{ $config != null ? $config->abrir_modal_cartao : 0 }}">
<input type="hidden" id="inp-senha_manipula_valor" value="{{ $config != null ? $config->senha_manipula_valor : '' }}">
@else
<input type="hidden" id="inp-abrir_modal_cartao" value="0">
<input type="hidden" id="inp-senha_manipula_valor" value="">
@endif
<input type="hidden" id="inp-finalizacao_pdv" value="{{ __finalizacaoPdv() }}">

<div class="row">
	<div class="col-md-6 col-12">
		<div class="d-flex align-items-center">
			<div class="w-100 text-gray-600 position-relative">

				<div class="input-group flex-nowrap">
					<span class="input-group-text" id="basic-addon1"><i class="ri-search-line"></i></span>
					<input autocomplete="off" type="text" id="inp-pesquisa" class="form-control border border-gray-300 py-3 pr-3" placeholder="Pesquise produto por código ou nome">
				</div>
				<div class="results-list d-none">
					
				</div>

			</div>
		</div>
	</div>
	<div class="col-md-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-3">
						<button type="button" class="btn btn-dark w-100" style="margin-top: -14px;" data-bs-toggle="modal" data-bs-target="#lista_precos"><i class="ri-cash-line"></i> Lista de preços</button>
					</div>
					<div class="col-md-3">
						<button type="button" class="btn btn-light w-100 btn-vendas-suspensas" style="margin-top: -14px;"  data-bs-toggle="modal" data-bs-target="#vendas_suspensas"><i class="ri-time-fill"></i> Vendas suspensas</button>
					</div>
					<div class="col-md-3">
						<a href="{{ route('frontbox.create') }}" type="button" class="btn btn-danger w-100 " style="margin-top: -14px;" ><i class="ri-shut-down-line"></i> Reiniciar venda</a>
					</div>

					@if(__countLocalAtivo() > 1 && $caixa->localizacao)
					<div class="col-md-3 text-end">
						<strong class="text-danger" style="margin-right: 5px;">{{ $caixa->localizacao->descricao }}</strong>
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-12 mt-1">
		<div class="card">

			<div class="row">
				<div class="card-body">
					<hr>
					<div class="col-12">
						<span class="badge bg-success">Itens da Venda</span>
					</div>
					<div class="itens-cart" style="height: 360px; overflow-y: scroll">
						@isset($item)
						@foreach ($item->itens as $key => $p)
						@php $code = rand(0,9999999999); @endphp
						<div class="row mt-1 products product-line-{{$code}}">
							<div class="col-md-2">
								<img src="{{ $p->produto->img }}" class="img-cart">
							</div>
							<input readonly type="hidden" name="key" class="form-control" value="{{ $key }}">
							<div class="col-md-5 text-left cart-data">
								<span class="title">{{ substr($p->produto->nome, 0, 30) }}</span><br>
								<span class="price">R$ {{ __moeda($p->valor_unitario) }}</span><br>
								<i class="ri-pencil-fill text-primary" onclick="editItem('{{$code}}', '{{$p->produto->id}}')"></i>
								<i class="ri-close-circle-line text-danger" onclick="removeItem('{{$code}}')"></i>
							</div>

							<input type="hidden" class="produto_id" name="produto_id[]" value="{{ $p->produto->id }}">
							<input type="hidden" class="quantidade" name="quantidade[]" value="{{ __moeda($p->quantidade) }}">
							<input type="hidden" class="valor_unitario" name="valor_unitario[]" value="{{ __moeda($p->valor_unitario) }}">
							<input type="hidden" class="subtotal_item" name="subtotal_item[]" value="{{ __moeda($p->sub_total) }}">

							<div class="col-md-5">
								<div class="d-flex" style="float: right;">
									<span class="increment-decrement btn btn-light rounded-circle" data-code="{{$code}}">-</span> 
									<input min="0" value="{{ $p->quantidade }}" class="fw-semibold cart-qty m-0 px-2 qtd-row"> 
									<span class="increment-decrement btn btn-light rounded-circle btn-incrementa" data-code="{{$code}}">+</span>
								</div>
							</div>
						</div>

						@endforeach
						@endif

						@if (isset($pedido) && isset($itens))
						@foreach ($itens as $key => $p)
						@php $code = rand(0,9999999999); @endphp
						<div class="row mt-1 products product-line-{{$code}}">
							<div class="col-md-2">
								<img src="{{ $p->produto->img }}" class="img-cart">
							</div>
							<input readonly type="hidden" name="key" class="form-control" value="{{ $key }}">
							<div class="col-md-5 text-left cart-data">
								<span class="title">{{ substr($p->produto->nome, 0, 30) }}</span><br>
								<span class="price">R$ {{ __moeda($p->quantidade*$p->valor_unitario )}}</span><br>
								<i class="ri-pencil-fill text-primary" onclick="editItem('{{$code}}', '{{$p->produto->id}}')"></i>
								<i class="ri-close-circle-line text-danger" onclick="removeItem('{{$code}}')"></i>
							</div>

							<input type="hidden" class="produto_id" name="produto_id[]" value="{{ $p->produto->id }}">
							<input type="hidden" class="quantidade" name="quantidade[]" value="{{ $p->quantidade }}">
							<input type="hidden" class="valor_unitario" name="valor_unitario[]" value="{{ $p->valor_unitario }}">
							<input type="hidden" class="subtotal_item" name="subtotal_item[]" value="{{ $p->sub_total }}">

							<div class="col-md-5">
								<div class="d-flex" style="float: right;">
									<span class="increment-decrement btn btn-light rounded-circle" data-code="{{$code}}">-</span> 
									<input min="0" value="{{ number_format($p->quantidade) }}" class="fw-semibold cart-qty m-0 px-2 qtd-row"> 
									<span class="increment-decrement btn btn-light rounded-circle btn-incrementa" data-code="{{$code}}">+</span>
								</div>
							</div>
						</div>
						@endforeach
						@endif

						@if (isset($servicos))
						@foreach ($servicos as $key => $servico)
						@php $code = rand(0,9999999999); @endphp
						
						@endforeach
						@endif

					</div>
				</div>
			</div>

			<div class="row">
				<div class="card-body">
					<hr>

					<div class="row g-1">
						<div class="col-md-4 col-12">
							<label class="form-label">Desconto</label>
							<div class="input-group">
								<input id="inp-desconto" type="tel" class="form-control moeda inp-modal-pass" value="{{ isset($item) ? __moeda($item->desconto) : '' }}">
								<input type="hidden" name="desconto" id="inp-valor_desconto" value="{{ isset($item) ? __moeda($item->desconto) : '' }}">
								<span class="input-group-append">
									<select class="form-select" id="inp-tipo_desconto" name="tipo_desconto">
										<option value="%">%</option>
										<option value="R$">R$</option>
									</select>
								</span>
							</div>
						</div>
						@isset($acrescimo)
						<div class="col-md-4 col-12">
							<label class="form-label">Acréscimo</label>
							<div class="input-group">
								<input value="{{ __moeda($acrescimo) }}" id="inp-acrescimo" type="tel" class="form-control moeda inp-modal-pass">
								<input type="hidden" name="acrescimo" id="inp-valor_acrescimo" value="{{ __moeda($acrescimo) }}">

								<span class="input-group-append">
									<select class="form-select" id="inp-tipo_acrescimo" name="tipo_acrescimo">
										<option value="R$">R$</option>
										<option value="%">%</option>
									</select>
								</span>
							</div>
						</div>
						@else
						<div class="col-md-4 col-12">
							<label class="form-label">Acréscimo</label>
							<div class="input-group">
								<input value="{{ isset($item) ? __moeda($item->acrescimo) : '' }}" id="inp-acrescimo" type="tel" class="form-control moeda inp-modal-pass">
								<input type="hidden" name="acrescimo" id="inp-valor_acrescimo" value="{{ isset($item) ? __moeda($item->acrescimo) : '' }}">

								<span class="input-group-append">
									<select class="form-select" id="inp-tipo_acrescimo" name="tipo_acrescimo">
										<option value="%">%</option>
										<option value="R$">R$</option>
									</select>
								</span>
							</div>
						</div>
						@endif

						<div class="col-md-4 col-12">
							<label class="form-label">Frete</label>
							<div class="input-group">
								<input value="{{ isset($item) ? __moeda($item->valor_frete) : '' }}" id="inp-valor_frete" type="tel" class="form-control moeda">

								<span class="input-group-append">
									<button type="button" class="btn btn-dark" onclick="modalFrete()">
										<i class="ri-truck-line"></i>
									</button>
								</span>
							</div>
						</div>
						<div class="col-12">
							<label>Cliente</label>
							<div class="input-group flex-nowrap">
								<select id="inp-cliente_id" name="cliente_id" class="cliente_id">
									@if(isset($item) && $item->cliente)
									<option value="{{ $item->cliente_id }}">{{ $item->cliente->razao_social }}</option>
									@endif
								</select>
								@can('clientes_create')
								<button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modal_novo_cliente" type="button">
									<i class="ri-add-circle-fill"></i>
								</button>
								@endcan
							</div>
						</div>
						<div class="col-12 mt-1">
							{!! Form::select('funcionario_id', 'Vendedor')
							->options(isset($item) && $item->funcionario ? [$item->funcionario->id => $item->funcionario->nome] : [])
							!!}
						</div>
						<div class="col-12 mt-2">
							<button type="button" id="btn-finalizar" class="btn btn-lg w-100 btn-success">
								Finalizar R$ <strong class="total">0,00</strong>
							</button>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Produtos -->
	<div class="col-md-8 col-12 mt-1">

		<div class="card">
			<div class="card-body">

				<div class="row" style="height: 732px;">
					<div class="col-md-9">
						<br>
						<div class="row div-produtos">
							@include('front_box.partials_form2.produtos')
						</div>
					</div>

					<div class="col-md-3 col-12 mt-3">

						<div class="list-group div-categorias">
							@include('front_box.partials_form2.categorias')
						</div>

						<div class="list-group div-marcas">
							@include('front_box.partials_form2.marcas')
						</div>

						<div class="list-group">
							
							<button type="button" data-bs-toggle="modal" data-bs-target="#suprimento_caixa" class="btn btn-dark w-100 mt-1">
								<i class="ri-add-box-line"></i>
								Suprimento de Caixa
							</button>

							<button type="button" data-bs-toggle="modal" data-bs-target="#sangria_caixa" class="btn btn-danger w-100 mt-1">
								<i class="ri-checkbox-indeterminate-line"></i>
								Sangria de Caixa
							</button>
							@if($isVendaSuspensa == 0)
							<button type="button" id="btn-suspender" class="btn btn-light w-100 mt-1">
								<i class="ri-timer-line"></i>
								Suspender Venda
							</button>
							@else
							<a href="{{ route('frontbox.create') }}" class="btn btn-light w-100 mt-1">
								<i class="ri-refresh-line"></i>
								Nova Venda
							</a>
							@endif

							<a href="{{ route('frontbox.index')}}" class="btn btn-primary w-100 mt-1">
								<i class="ri-arrow-left-s-line"></i>
								Sair do PDV
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

@include('modals._info_produto')
@include('modals._edit_item_pdv')
@include('modals._finalizar_pdv2')
@include('modals._variacao', ['not_submit' => true])
@include('modals._fatura_venda')
@include('modals._frete')

@include('modals._lista_precos')
@include('modals._cashback')
@include('modals._vendas_suspensas')

@section('js')
<script>
    var senhaAcao = "";

    @if(isset($config) && strlen(trim($config->senha_manipula_valor)) > 1)
        senhaAcao = "{{ $config->senha_manipula_valor }}";
    @endif
</script>
<script type="text/javascript" src="/js/controla_conta_empresa.js"></script>
<script src="/js/frente_caixa2.js" type=""></script>
<script src="/js/novo_cliente.js"></script>

<script type="text/javascript">

	@if(Session::has('sangria_id'))
	window.open(path_url + 'sangria-print/' + {{ Session::get('sangria_id') }}, "_blank")
	@endif
	@if(Session::has('suprimento_id'))
	window.open(path_url + 'suprimento-print/' + {{ Session::get('suprimento_id') }}, "_blank")
	@endif

	$('.btn-novo-cliente').click(() => {
		$('.modal-select-cliente .btn-close').trigger('click')
		$('#modal_novo_cliente').modal('show')
	})

</script>
@endsection