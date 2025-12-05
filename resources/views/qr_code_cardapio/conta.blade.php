@extends('qr_code_cardapio.default', ['title' => 'Minha Conta'])
@section('content')

@section('css')
<style type="text/css">
	.lni-star:hover{
		cursor: pointer;
	}

	.text-warning-star{
		color: #C85F42;
	}

	select{
		height: 45px !important;
	}
</style>
@endsection

<div class="minfit" style="min-height: 472px;">
	<div class="middle">
		<div class="container nopaddmobile">
			<div class="row rowtitle">
				<div class="col-md-12">
					<div class="title-icon">
						<span>Minha Conta <strong style="color: var(--main) !important">{{ $pedido->_mesa->nome }}</strong></span>

					</div>
					<div class="bread-box">
						<div class="bread">
							<a href="{{ route('cardapio.index', ['link='.$link]) }}"><i class="lni lni-home"></i></a>
							<span>/</span>
							<a href="{{ route('cardapio.carrinho', ['link='.$link]) }}">Meu carrinho</a>
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
										<th>Valor Unit.</th>
										<th>SubTotal</th>
										<th>Detalhes</th>
										<th>Estado</th>
										<th>Horário do pedido</th>
									</tr>
								</thead>
								<tbody>
									@if($pedido != null && sizeof($pedido->itens) > 0)
									@foreach($pedido->itens as $i)
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
										
										<td class="td-quantidade" style="font-weight: bold;">
											{{ number_format($i->quantidade, 0) }}x
										</td>
										<td class="td-valor">
											<span>Valor unit.:</span>
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

										<td class="td-">
											<div class="line ">
												<strong>{{ $i->observacao }}</strong>
											</div>
										</td>

										<td class="">
											@if($i->estado == 'novo')
											<label class="text-primary">Novo</label>
											@elseif($i->estado == 'pendente')
											<label class="text-warning">Pendente</label>
											@elseif($i->estado == 'preparando')
											<label class="text-info">Preparando</label>
											@elseif($i->estado == 'finalizado')
											<label class="text-success">Finalizado</label>
											@endif
										</td>

										<td class="td-">
											{{ \Carbon\Carbon::parse($i->created_at)->format('H:i') }}
											@if($i->estado == 'finalizado')
											/ <strong class="text-success">{{ \Carbon\Carbon::parse($i->updated_at)->format('H:i') }}</strong>
											@endif
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
										<td colspan="8"><span class="nulled">Nenhum item foi adicionado</span></td>
									</tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
					<div class="linha-subtotal">
						@if($pedido)
						<div class="row error-pedido-minimo error-pedido-minimo-sacola">
							<div class="col-md-12">
								<input class="fake-hidden" id="inp-carrinho_id" value="{{ $pedido->id }}">
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-9">
								<div class="subtotal"><strong>Subtotal:</strong> <span class="subtotal-valor">R$ {{ __moeda($subtotal) }}</span></div>
								
							</div>

							@if($pedido->em_atendimento)
							<div class="col-md-3">
								<button type="button" class="botao-acao btn-fechar-mesa"><span>Fechar Mesa</span></button>
								<div class="subtotal"><strong>Total da mesa:</strong> <span class="subtotal-valor">R$ {{ __moeda($pedido->total) }}</span></div>
							</div>
							@else
							<div class="col-md-12 text-center">
								<br>
								<h5>Mesa Finalizada</h5>
							</div>
							@endif

						</div>

						@endif

						<hr>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="mdfecharmesa">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header fixed-bottom">
				<h3 class="modal-title pull-left" id="tituloProduto">Finalizando</h3>
				<button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
					<i class="lni lni-close"></i>
				</button>
			</div>
			<div class="modal-body">
				<form class="row" action="{{ route('cardapio.pedir-fechar') }}">
					@csrf
					<input type="hidden" name="link" value="{{ $link }}" />
					<input type="hidden" name="avaliacao" id="avaliacao" />

					<div class="col-md-12" style="text-align: center;">
						@for($i=1; $i<=5; $i++)
						<i style="margin-right: 10px" onclick="clickStart('{{$i}}')" class="lni lni-star star-{{$i}}"></i>
						@endfor
					</div>

					<div class="col-md-12" style="margin-top: 20px">
						<input type="text" name="observacao" placeholder="Observação">
					</div>
					<div class="col-md-12" style="margin-top: 20px">
						<select name="tipo_pagamento" class="form-control">
							<option value="">Selecione o tipo de pagamento</option>
							@foreach($tiposPagamento as $t)
							<option value="{{ $t }}">{{ $t }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-12" style="margin-top: 20px">
						<button type="submit" class="botao-acao" style="float: right;"><span>Fechar Mesa</span></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@section('js')
<script src="/food-files/js/carrinho_cardapio.js"></script>
@endsection
