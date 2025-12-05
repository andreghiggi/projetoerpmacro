@extends('layouts.app', ['title' => 'Fretes'])
@section('content')
<div class="mt-1">
	<div class="row">
		<div class="card">
			<div class="card-body">
				<div class="row">
					@can('frete_create')
					<div class="col-md-2">
						<a href="{{ route('fretes.create') }}" class="btn btn-success">
							<i class="ri-add-circle-fill"></i>
							Novo Frete
						</a>
					</div>
					@endcan

					<div class="col-md-8"></div>


				</div>
				<hr class="mt-3">
				<div class="col-lg-12">
					{!!Form::open()->fill(request()->all())
					->get()
					!!}
					<div class="row mt-3 g-1">
						<div class="col-md-4">
							{!!Form::select('cliente_id', 'Cliente')
							->attrs(['class' => 'select2'])
							->options($cliente != null ? [$cliente->id => $cliente->info] : [])
							!!}
						</div>
						<div class="col-md-2">
							{!!Form::date('start_date', 'Data inicial cadastro')
							!!}
						</div>
						<div class="col-md-2">
							{!!Form::date('end_date', 'Data final cadastro')
							!!}
						</div>

						<div class="col-md-2">
							{!!Form::select('estado', 'Estado',
							[
							'' => 'Todos',
							'em_carregamento' => 'Em carregamento',
							'em_viagem' => 'Em viagem',
							'finalizado' => 'Finalizado',
							])
							->attrs(['class' => 'form-select'])
							!!}
						</div>

						<div class="col-md-2">
							{!!Form::select('veiculo_id', 'Veículo')
							->attrs(['class' => 'select2'])
							->options($veiculo != null ? [$veiculo->id => $veiculo->info] : [])
							!!}
						</div>
						@if(__countLocalAtivo() > 1)
						<div class="col-md-2">
							{!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
							->attrs(['class' => 'select2'])
							!!}
						</div>
						@endif

						<div class="col-lg-4 col-12">
							<br>

							<button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
							<a id="clear-filter" class="btn btn-danger" href="{{ route('fretes.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
						</div>
					</div>
					{!!Form::close()!!}
				</div>

				<div class="col-md-12 mt-3">
					<div class="table-responsive">
						<table class="table table-striped table-centered mb-0">
							<thead class="table-dark">
								<tr>
									<th>#</th>
									<th>Cliente</th>
									<th>Veículo</th>
									<th>Valor do frete</th>
									<th>Total de despesas</th>
									@if(__countLocalAtivo() > 1)
									<th>Local</th>
									@endif
									<th>Data inicial da viagem</th>
									<th>Data final da viagem</th>
									<th>Data de cadastro</th>
									<th>Estado</th>
									<th>Ações</th>
								</tr>
							</thead>
							<tbody>
								@forelse($data as $item)
								<tr>
									<td data-label="#">{{ $item->numero_sequencial }}</td>
									<td data-label="Cliente">{{ $item->cliente->info }}</td>
									<td data-label="Veículo">{{ $item->veiculo->info }}</td>
									<td data-label="Valor do frete">{{ __moeda($item->total) }}</td>
									<td data-label="Total de despesas">{{ __moeda($item->total_despesa) }}</td>
									@if(__countLocalAtivo() > 1)
									<td data-label="Local">{{ $item->localizacao->descricao }}</td>
									@endif
									<td data-label="Data inicial da viagem">{{ __data_pt($item->data_inicio, 0) }}</td>
									<td data-label="Data final da viagem">{{ __data_pt($item->data_fim, 0) }}</td>
									<td data-label="Data de cadastro">{{ __data_pt($item->created_at) }}</td>
									<td data-label="Estado">
										@if($item->estado == 'em_carregamento')
										<span class="btn btn-sm btn-warning text-white">Em carregamento</span>
										@elseif($item->estado == 'em_viagem')
										<span class="btn btn-primary text-white btn-sm">Em viagem</span>
										@else
										<span class="btn btn-success text-white btn-sm">Finalizado</span>
										@endif
									</td>
									<td>
										<form action="{{ route('fretes.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 220px">
											@method('delete')
											@csrf

											@can('frete_edit')
											<a class="btn btn-warning btn-sm" href="{{ route('fretes.edit', $item->id) }}">
												<i class="ri-edit-line"></i>
											</a>
											@endcan

											@can('frete_delete')
											<button type="button" class="btn btn-danger btn-sm btn-delete">
												<i class="ri-delete-bin-line"></i>
											</button>
											@endcan

											<a class="btn btn-light btn-sm" title="Detalhes" href="{{ route('fretes.show', $item->id) }}">
												<i class="ri-eye-line"></i>
											</a>
										</form>
									</td>
								</tr>
								@empty
								<tr>
									<td colspan="10" class="text-center">Nada encontrado</td>
								</tr>
								@endforelse
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3">Soma</td>
									<td>R$ {{ __moeda($data->sum('total')) }}</td>
									<td colspan="6">R$ {{ __moeda($data->sum('total_despesa')) }}</td>
								</tr>
							</tfoot>
						</table>

					</div>
					<br>
					{!! $data->appends(request()->all())->links() !!}
				</div>

			</div>
		</div>
	</div>
</div>

@endsection
