@extends('layouts.app', ['title' => 'Planejamento #' . $item->numero_sequencial])
@section('content')



<div class="row">
	<div class="col-md-4 mt-2">
		<div class="card text-center">
			<div style="text-align: right; margin-top: 10px; margin-right: 8px;">
				<a href="{{ route('planejamento-custo.index') }}" class="btn btn-danger btn-sm px-3">
					<i class="ri-arrow-left-double-fill"></i>Voltar
				</a>
			</div>
			<div class="card-body">
				<h4>Planejamento #{{ $item->numero_sequencial }}</h4>

				<button type="button" class="btn btn-sm btn-dark" href="{{ route('planejamento-custo.index') }}" data-bs-toggle="modal" data-bs-target="#modal_estado">
					<i class="ri-refresh-fill"></i>
					Alterar estado
				</button>
				<a class="btn btn-sm btn-warning" href="{{ route('planejamento-custo.edit', [$item->id]) }}">
					<i class="ri-pencil-fill"></i>
					Editar
				</a>


				<div class="text-start mt-3">
					<h4 class="fs-13 text-uppercase">Descrição:</h4>
					<p class="text-muted mb-3">
						{{ $item->descricao }}
					</p>

					<h4 class="fs-13 text-uppercase">Observação:</h4>
					<p class="text-muted mb-3">
						{{ $item->observacao }}
					</p>

					<p class="text-muted mb-2"><strong>Cliente:</strong> <span class="ms-2">{{ $item->projeto->cliente->razao_social }}</span></p>
					<p class="text-muted mb-2"><strong>CPF/CNPJ:</strong> <span class="ms-2">{{ $item->projeto->cliente->cpf_cnpj }}</span></p>
					<p class="text-muted mb-2"><strong>Telefone:</strong> <span class="ms-2">{{ $item->projeto->cliente->telefone }}</span></p>
					<p class="text-muted mb-2"><strong>Email:</strong> <span class="ms-2">{{ $item->projeto->cliente->email }}</span></p>
					<p class="text-muted mb-2"><strong>Cidade:</strong> <span class="ms-2">{{ $item->projeto->cliente->cidade ? $item->projeto->cliente->cidade->info : '--' }}</span></p>
					{!! $item->_estado() !!}

					@if(sizeof($item->itensProposta) > 0)
					<div class="row">
						<p class="text-muted"><strong>Valor Final Proposta:</strong> <span class="text-success">R$ {{ __moeda($item->total_final) }}</span></p>
					</div>
					@endif
					<div class="row">
						<div class="col-md-12">
							@if($item->estado == 'proposta')

							<a class="btn btn-dark" href="{{ route('planejamento-custo.proposta', [$item->id]) }}" target="_blank">
								<i class="ri-bring-forward"></i> 
								@if(sizeof($item->itensProposta) == 0)
								Criar Proposta
								@else
								Nova Proposta
								@endif
							</a>

							@endif
							@if($item->estado != 'cotacao' && sizeof($item->itensProposta) > 0)
							<a class="btn btn-light" href="{{ route('planejamento-custo.imprimir-proposta', [$item->id]) }}" target="_blank">
								<i class="ri-printer-line"></i> Imprimir Proposta
							</a>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card">

			<div class="card-body">

				@if($item->arquivo)
				<a target="_blank" class="btn btn-sm btn-light" href="{{ route('planejamento-custo.preview', [$item->id]) }}">
					<i class="ri-file-fill"></i>
					Visualizar Arquivo
				</a>
				@endif

				<div class="d-flex justify-content-between align-items-center mt-2">
					<h4 class="header-title">Histórico de alteração</h4>
				</div>

				<div class="inbox-widget">
					@foreach($item->logs as $l)
					<div class="inbox-item">
						<p class="inbox-item-author">{{ $l->usuario->name }} - {{ __data_pt($l->created_at) }}</p>

						@if($l->estado_anterior == '' && $l->estado_alterado == '')
						<h5 class='badge bg-light text-dark'>PROPOSTA</h5>
						@else
						<p class="inbox-item-author">{!! $l->_estadoAnterior() !!} / {!! $l->_estadoAlterado() !!}</p>
						@endif

						@if($l->observacao)
						<p class="inbox-item-text">{{ $l->observacao }}</p>
						@endif
					</div>
					@endforeach
				</div>
			</div>

		</div>
	</div>


	<div class="col-md-8 mt-2">
		<div class="card">
			<div class="card-body">

				<p class="text-muted">Valores de custo</p>
				<ul class="nav nav-pills bg-nav-pills nav-justified mb-3" role="tablist">
					<li class="nav-item" role="presentation">
						<a href="#produtos" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-start rounded-0 active" aria-selected="false" role="tab" tabindex="-1">
							Produtos
						</a>
					</li>
					<li class="nav-item" role="presentation">
						<a href="#servicos" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0" aria-selected="false" role="tab" tabindex="-1">
							Mão de Obra
						</a>
					</li>
					<li class="nav-item" role="presentation">
						<a href="#servicos-terceiro" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0" aria-selected="false" role="tab" tabindex="-1">
							Serviços de Terceiros
						</a>
					</li>

					<li class="nav-item" role="presentation">
						<a href="#custos-adm" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0" aria-selected="false" role="tab" tabindex="-1">
							Custos Administrativos
						</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane show active" id="produtos">
						
						<div class="table-responsive">
							<table class="table table-striped table-centered mb-0">
								<thead class="table-dark">
									<tr>
										<th>Produto</th>
										<th>Quantidade</th>
										<th>Valor unitário</th>
										<th>Sub total</th>
									</tr>
								</thead>
								<tbody>
									@foreach($item->produtos as $i)
									<tr>
										<td>{{ $i->descricao() }}</td>
										<td>
											@if(!$i->produto->unidadeDecimal())
											{{ number_format($i->quantidade, 0, '.', '') }}
											@else
											{{ number_format($i->quantidade, 3, '.', '') }}
											@endif
										</td>
										<td>{{ __moeda($i->valor_unitario) }}</td>
										<td>{{ __moeda($i->sub_total) }}</td>
									</tr>
									@endforeach
								</tbody>
								<!-- <tfoot>
									<tr>
										<td colspan="3">Soma</td>
										<td>{{ __moeda($item->produtos->sum('sub_total')) }}</td>
									</tr>
								</tfoot> -->
							</table>
						</div>


						<a class="btn mt-2" href="{{ route('planejamento-custo.cotacao', [$item->id]) }}">Cotações</a>

					</div>

					<div class="tab-pane" id="servicos">
						
						<div class="table-responsive">
							<table class="table table-striped table-centered mb-0">
								<thead class="table-dark">
									<tr>
										<th>Serviço</th>
										<th>Quantidade</th>
										<th>Valor unitário</th>
										<th>Sub total</th>
									</tr>
								</thead>
								<tbody>
									@foreach($item->servicos as $i)
									<tr>
										<td>{{ $i->servico->nome }}</td>
										<td>
											{{ number_format($i->quantidade, 0, '.', '') }}
										</td>
										<td>{{ __moeda($i->valor_unitario) }}</td>
										<td>{{ __moeda($i->sub_total) }}</td>
									</tr>
									@endforeach
								</tbody>
								
							</table>
						</div>

					</div>

					<div class="tab-pane" id="servicos-terceiro">
						
						<div class="table-responsive">
							<table class="table table-striped table-centered mb-0">
								<thead class="table-dark">
									<tr>
										<th>Serviço</th>
										<th>Quantidade</th>
										<th>Valor unitário</th>
										<th>Sub total</th>
									</tr>
								</thead>
								<tbody>
									@foreach($item->servicosTerceiro as $i)
									<tr>
										<td>{{ $i->servico->nome }}</td>
										<td>
											{{ number_format($i->quantidade, 0, '.', '') }}
										</td>
										<td>{{ __moeda($i->valor_unitario) }}</td>
										<td>{{ __moeda($i->sub_total) }}</td>
									</tr>
									@endforeach
								</tbody>
								
							</table>
						</div>

					</div>

					<div class="tab-pane" id="custos-adm">
						
						<div class="table-responsive">
							<table class="table table-striped table-centered mb-0">
								<thead class="table-dark">
									<tr>
										<th>Descrição</th>
										<th>Quantidade</th>
										<th>Valor unitário</th>
										<th>Sub total</th>
									</tr>
								</thead>
								<tbody>
									@foreach($item->custosAdm as $i)
									<tr>
										<td>{{ $i->descricao }}</td>
										<td>
											{{ number_format($i->quantidade, 0, '.', '') }}
										</td>
										<td>{{ __moeda($i->valor_unitario) }}</td>
										<td>{{ __moeda($i->sub_total) }}</td>
									</tr>
									@endforeach
								</tbody>
								
							</table>
						</div>

					</div>
				</div>

				<hr>
				<div class="row">
					<div class="col-md-12">
						<div class="float-end">
							<p><b>Produtos:</b> <span class="float-end">R$ {{ __moeda($item->produtos->sum('sub_total')) }}</span></p>
							<p><b>Mão de obra:</b> <span class="float-end">R$ {{ __moeda($item->servicos->sum('sub_total')) }}</span></p>
							<p><b>Serviços terceiro:</b> <span class="float-end">R$ {{ __moeda($item->servicosTerceiro->sum('sub_total')) }}</span></p>
							<p><b>Custos administrativos:</b> <span class="float-end" style="margin-left: 10px;"> R$ {{ __moeda($item->custosAdm->sum('sub_total')) }}</span></p>
							<!-- <p><b>Desconto:</b> <span class="float-end">-R$ {{ __moeda($item->desconto) }}</span></p> -->

							<h3>TOTAL CUSTO <strong class="text-primary">R$ {{ __moeda($item->servicos->sum('sub_total') + $item->custosAdm->sum('sub_total') + $item->produtos->sum('sub_total') + $item->servicosTerceiro->sum('sub_total') - $item->desconto) }}</strong></h3>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-8 mt-2">
	</div>
</div>

@include('planejamento_custo.partials.modal_estado')

@endsection
@section('js')

@endsection
