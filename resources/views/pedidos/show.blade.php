@extends('layouts.app', ['title' => 'Comanda ' . $item->comanda])
@section('css')
<style type="text/css">
	.card-hover:hover{
		cursor: pointer;
	}
</style>
@endsection
@section('content')
<div class="mt-3">
	<div class="row">
		<div id="print"></div>

		<input type="hidden" id="impressao_sem_janela_cupom" value="{{ $configGeral ? $configGeral->impressao_sem_janela_cupom : 0 }}">
		<div class="col-12 col-lg-4">
			<div class="card">
				<div class="card-body">
					<h5 class="text-center">PRODUTOS</h5>
					<form class="row" method="post" action="{{ route('pedidos-cardapio.store-item', [$item->id]) }}">
						@csrf

						<input type="hidden" id="tipo_divisao_pizza" value="{{ $config != null ? $config->valor_pizza : 'divide' }}">

						<div class="col-md-12">
							{!!Form::select('produto_cardapio', 'Produto')->required()
							->attrs(['class' => 'produto_cardapio'])
							!!}
						</div>

						<div class="col-md-6 col-12 mt-2">
							{!!Form::tel('quantidade', 'Quantidade')
							->required()
							->attrs(['class' => 'moeda'])
							!!}
						</div>

						<div class="col-md-6 col-12 mt-2">
							{!!Form::tel('valor_unitario', 'Valor unitário')
							->required()
							->attrs(['class' => 'moeda'])
							!!}
						</div>

						<div class="col-md-12 mt-2">
							<button type="button" class="btn w-100 btn-dark" id="btn-adicionais">
								<i class="ri-shopping-basket-fill"></i>
								Definir adicionais
							</button>
						</div>

						<div class="col-md-12 mt-2 adicionaisescolhidos">
						</div>

						<div class="col-md-12 col-12 mt-2">
							{!!Form::text('observacao', 'Observação')
							!!}
						</div>

						<div class="col-12 mt-2 div-tp-carne d-none">
							{!!Form::select('ponto_carne', 'Ponto da carne', ['' => 'Selecione'] +  App\Models\Produto::pontosDaCarne())
							->attrs(['class' => 'form-select'])
							!!}
						</div>

						<div class="col-md-6 col-12 mt-2">
							{!!Form::tel('sub_total', 'Subtotal')
							->required()
							->readonly()
							->attrs(['class' => 'moeda'])
							!!}
						</div>

						<div class="col-md-6 col-12 mt-2">
							{!!Form::select('estado', 'Estado', 
							[
							'novo' => 'Novo', 
							'pendente' => 'Pendente', 
							'preparando' => 'Preparando', 
							'finalizado' => 'Finalizando'
							])
							->attrs(['class' => 'form-select'])
							->required()
							!!}
						</div>

						<input type="hidden" id="adicionais-hidden" name="adicionais">
						<input type="hidden" id="pizzas-hidden" name="pizzas">
						<input type="hidden" id="tamanho_id-hidden" name="tamanho_id">
						<div class="col-md-12 col-12 mt-4">
							<button type="submit" class="btn w-100 btn-success">
								<i class="ri-checkbox-circle-fill"></i>
								Adicionar
							</button>
						</div>
					</form>

					@if($config != null && $config->incluir_servico)
					<br>
					<hr>
					<h5 class="text-center">SERVIÇOS</h5>
					<form class="row" method="post" action="{{ route('pedidos-cardapio.store-servico', [$item->id]) }}">
						@csrf

						<div class="col-md-12">
							{!!Form::select('servico_id', 'Serviço')->required()
							->attrs(['class' => ''])
							!!}
						</div>

						<div class="col-md-6 col-12 mt-2">
							{!!Form::tel('quantidade', 'Quantidade')
							->required()
							->attrs(['class' => 'moeda qtd_servico'])
							!!}
						</div>

						<div class="col-md-6 col-12 mt-2">
							{!!Form::tel('valor_unitario', 'Valor unitário')
							->required()
							->attrs(['class' => 'moeda valor_unitario_servico'])
							!!}
						</div>

						<div class="col-md-12 col-12 mt-2">
							{!!Form::text('observacao', 'Observação')
							!!}
						</div>

						<div class="col-md-6 col-12 mt-2">
							{!!Form::tel('sub_total', 'Subtotal')
							->required()
							->readonly()
							->attrs(['class' => 'moeda sub_total_servico'])
							!!}
						</div>

						<div class="col-md-6 col-12 mt-2">
							{!!Form::select('estado', 'Estado', 
							[
							'novo' => 'Novo', 
							'pendente' => 'Pendente', 
							'preparando' => 'Preparando', 
							'finalizado' => 'Finalizando'
							])
							->attrs(['class' => 'form-select'])
							->required()
							!!}
						</div>

						<div class="col-md-12 col-12 mt-4">
							<button type="submit" class="btn w-100 btn-dark">
								<i class="ri-checkbox-circle-fill"></i>
								Adicionar
							</button>
						</div>
					</form>
					@endif

				</div>
			</div>
		</div>

		<div class="col-12 col-lg-8">
			<div class="card">
				<div class="card-body">
					<div class="col-12">
						<h3>ITENS <strong class="text-success">#{{ $item->comanda }}</strong></h3>

						<button class="float-end btn btn-dark" onclick="print('{{ $item->id }}')">
							<i class="ri-printer-line"></i>
							Imprimir
						</button>
					</div>

					
					<div class="table-responsive col-12" style="min-height: 300px;">
						<table class="table">
							<thead>
								<tr>
									<th>Produto</th>
									<th>Quantidade</th>
									<th>Valor unitário</th>
									<th>Subtotal</th>
									<th>Observação</th>
									<th>Ações</th>
								</tr>
							</thead>
							<tbody>
								@foreach($item->itens as $i)
								<tr class="bg-{{ $i->estado }}">
									<td>
										{{ $i->produto->nome }}
										@if($i->funcionario)
										<br> <span style="color: red; font-size: 11px">garçom: {{ $i->funcionario->nome }}</span>
										@endif
									</td>
									<td>{{ __moeda($i->quantidade) }}</td>
									<td>{{ __moeda($i->valor_unitario) }}</td>
									<td>{{ __moeda($i->sub_total) }}</td>
									<td>
										@if($i->observacao == '')
										<button class="btn btn-sm">
											<i class="ri-sticky-note-line"></i>
										</button>
										@else
										<button class="btn btn-sm btn-dark" onclick="noteSwal('{{ $i->observacao }}')">
											<i class="ri-sticky-note-line"></i>
										</button>
										@endif
									</td>
									<td>
										<form action="{{ route('pedidos-cardapio.destroy-item', $i->id) }}" method="post" id="form-{{$i->id}}">
											@csrf
											@method('delete')
											<button type="submit" title="Deletar" class="btn btn-danger btn-delete btn-sm"><i class="ri-delete-bin-2-line"></i></button>
										</form>
									</td>
								</tr>
								@if(sizeof($i->adicionais) > 0)
								<tr>
									<td></td>
									<td colspan="5" style="font-weight: bold; font-size: 13px;">Adicionais: {{ $i->getAdicionaisStr() }}</td>
								</tr>
								@endif

								@if($i->ponto_carne)
								<tr>
									<td></td>
									<td colspan="5" style="font-weight: bold; font-size: 13px;">Ponto da carme: <strong class="text-success">{{ $i->ponto_carne }}</strong></td>
								</tr>
								@endif

								@if(sizeof($i->pizzas) > 0)
								<tr>
									<td></td>
									<td colspan="5" style="font-weight: bold; font-size: 13px;">Sabores: 
										<strong class="text-success">
											@foreach($i->pizzas as $s)
											1/{{ sizeof($i->pizzas) }} {{ $s->sabor->nome }}
											@if(!$loop->last)
											|
											@endif
											@endforeach
										</strong>

										<span> - Tamanho: <strong class="text-info">{{ $i->tamanho ? $i->tamanho->nome : '--' }}</strong></span>
									</td>
								</tr>
								@endif
								@endforeach

								<!-- serviços -->

								@foreach($item->itensServico as $i)
								<tr class="bg-{{ $i->estado }}">
									<td>Servico: {{ $i->servico->nome }}</td>
									<td>{{ __moeda($i->quantidade) }}</td>
									<td>{{ __moeda($i->valor_unitario) }}</td>
									<td>{{ __moeda($i->sub_total) }}</td>
									<td>
										@if($i->observacao == '')
										<button class="btn btn-sm">
											<i class="ri-sticky-note-line"></i>
										</button>
										@else
										<button class="btn btn-sm btn-dark" onclick="noteSwal('{{ $i->observacao }}')">
											<i class="ri-sticky-note-line"></i>
										</button>
										@endif
									</td>
									<td>
										<form action="{{ route('pedidos-cardapio.destroy-item-servico', $i->id) }}" method="post" id="form-{{$i->id}}">
											@csrf
											@method('delete')
											<button type="submit" title="Deletar" class="btn btn-danger btn-delete btn-sm"><i class="ri-delete-bin-2-line"></i></button>
										</form>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>	

					<div class="row">
						<h5>estado dos itens</h5>
						<div class="col-lg-3 col-6">
							<h6 class="text-novo">
								<i class="ri-flag-2-fill"></i> novo
							</h6>
						</div>

						<div class="col-lg-3 col-6">
							<h6 class="text-pendente">
								<i class="ri-flag-2-fill"></i> pendente
							</h6>
						</div>

						<div class="col-lg-3 col-6">
							<h6 class="text-preparando">
								<i class="ri-flag-2-fill"></i> preparando
							</h6>
						</div>

						<div class="col-lg-3 col-6">
							<h6 class="text-finalizado">
								<i class="ri-flag-2-fill"></i> finalizado
							</h6>
						</div>
					</div>
					<hr>
					<div class="col-12">
						<a class="btn btn-lg btn-primary pull-right @if(!$item->status) disabled @endif" href="{{ route('pedidos-cardapio.finish', [$item->id])}}">
							<i class="ri-shopping-cart-2-line"></i>
							Finalizar <strong style="font-size: 25px; margin-left: 15px">R$ {{ __moeda($item->total) }}</strong>
						</a>
					</div>

					<div style="text-align: right; margin-top: -40px;">
						<a href="{{ route('pedidos-cardapio.index') }}" class="btn btn-sm btn-danger btn-sm px-3">
							<i class="ri-arrow-left-double-fill"></i>Voltar
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-adicionais" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Adicionais</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row adicionais">


				</div>

				<h4 class="mt-3">Subtotal: <strong class="subtotal_modal"></strong></h4>

			</div>
			<div class="modal-footer">
				<button id="btn-save-modal" type="button" class="btn btn-success" data-bs-dismiss="modal">Salvar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-pizza" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Selecione os sabores</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row m-2">
					<p class="text-danger">*selecione o tamanho para buscar os sabores</p>
					<div class="col-md-5 col-6">
						{!!Form::select('tamanho_id', 'Tamanho', ['' => 'Selecione'] + 
						$tamanhosPizza->pluck('info', 'id')->all())
						->attrs(['class' => 'form-select'])
						!!}
					</div>
				</div>
				<div class="row pizzas m-2 mt-4">
				</div>

				<div class="col-md-2 col-6 m-2 mt-3">
					{!!Form::tel('subtotal_modal', 'Subtotal')
					->required()
					->attrs(['class' => 'moeda'])
					!!}
				</div>
			</div>
			<div class="modal-footer">
				<button id="btn-save-sabores" type="button" class="btn btn-success">Salvar</button>
			</div>
		</div>
	</div>
</div>

@endsection

@section('js')
<script type="text/javascript" src="/js/pedido.js"></script>
@endsection
