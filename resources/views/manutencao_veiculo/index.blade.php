@extends('layouts.app', ['title' => 'Manutenção de Veículos'])
@section('content')
<div class="mt-1">
	<div class="row">
		<div class="card">
			<div class="card-body">
				<div class="row">
					@can('manutencao_veiculo_create')
					<div class="col-md-2">
						<a href="{{ route('manutencao-veiculos.create') }}" class="btn btn-success">
							<i class="ri-add-circle-fill"></i>
							Nova Manutenção
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
							{!!Form::select('fornecedor_id', 'Fornecedor')
							->attrs(['class' => 'select2'])
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
							'aguardando' => 'Aguardando',
							'em_manutencao' => 'Em manutenção',
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
							<a id="clear-filter" class="btn btn-danger" href="{{ route('manutencao-veiculos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
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
									<th>Fornecedor</th>
									<th>Veículo</th>
									<th>Valor</th>
									<th>Data de início</th>
									<th>Data de fim</th>
									<th>Data de cadastro</th>
									<th>Estado</th>
									<th>Ações</th>
								</tr>
							</thead>
							<tbody>
								@forelse($data as $item)
								<tr>
									<td data-label="#">{{ $item->numero_sequencial }}</td>
									<td data-label="Fornecedor">{{ $item->fornecedor->info }}</td>
									<td data-label="Veículo">{{ $item->veiculo->info }}</td>
									<td data-label="Valor">{{ __moeda($item->total) }}</td>
									<td data-label="Data de início">{{ __data_pt($item->data_inicio, 0) }}</td>
									<td data-label="Data de fim">{{ __data_pt($item->data_fim, 0) }}</td>
									<td data-label="Data de cadastro">{{ __data_pt($item->created_at) }}</td>
									<td data-label="Estado">
										@if($item->estado == 'aguardando')
										<span class="btn btn-sm btn-warning text-white">Aguardando</span>
										@elseif($item->estado == 'em_manutencao')
										<span class="btn btn-primary text-white btn-sm">Em manutenção</span>
										@else
										<span class="btn btn-success text-white btn-sm">Finalizado</span>
										@endif
									</td>
									<td>
										<form action="{{ route('manutencao-veiculos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 220px">
											@method('delete')
											@csrf

											@can('manutencao_veiculo_edit')
											<a class="btn btn-warning btn-sm" href="{{ route('manutencao-veiculos.edit', $item->id) }}">
												<i class="ri-edit-line"></i>
											</a>
											@endcan

											@can('manutencao_veiculo_delete')
											<button type="button" class="btn btn-danger btn-sm btn-delete">
												<i class="ri-delete-bin-line"></i>
											</button>
											@endcan

											<a class="btn btn-light btn-sm" title="Detalhes" href="{{ route('manutencao-veiculos.show', $item->id) }}">
												<i class="ri-eye-line"></i>
											</a>
										</form>
									</td>
								</tr>
								@empty
								<tr>
									<td colspan="9" class="text-center">Nada encontrado</td>
								</tr>
								@endforelse
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3">Soma</td>
									<td colspan="6">R$ {{ __moeda($data->sum('total')) }}</td>
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
