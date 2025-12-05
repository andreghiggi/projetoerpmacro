<div class="table-responsive">
	<table class="table table-striped table-centered mb-0">
		<thead class="table-dark">
			<tr>
				<th style="width: 25%">Produto</th>
				<th>Valor original</th>
				<th>Valor promoção</th>
				<th>Status</th>
				<th>Data de cadastro</th>
				<th>Data de início</th>
				<th>Data de fim</th>
				<th width="10%">Ações</th>
			</tr>
		</thead>
		<tbody>
			@forelse($data as $item)
			<tr>
				<td data-label="Produto">{{ $item->produto->nome }}</td>
				<td data-label="Valor original">{{ __moeda($item->valor_original) }}</td>
				<td data-label="Valor promoção">{{ __moeda($item->valor) }}</td>
				<td data-label="Status">
					@if($item->status)
					<i class="ri-checkbox-circle-fill text-success"></i>
					@else
					<i class="ri-close-circle-fill text-danger"></i>
					@endif
				</td>
				<td data-label="Data de cadastro">{{ __data_pt($item->created_at) }}</td>
				<td data-label="Data de início">{{ __data_pt($item->data_inicio, 0) }}</td>
				<td data-label="Data de fim">{{ __data_pt($item->data_fim, 0) }}</td>
				<td>
					<form style="width: 150px;" action="{{ route('promocao-produtos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
						@method('delete')
						@can('promocao_produtos_edit')
						<a class="btn btn-warning btn-sm text-white" href="{{ route('promocao-produtos.edit', [$item->id]) }}">
							<i class="ri-pencil-fill"></i>
						</a>
						@endcan
						@csrf
						@can('promocao_produtos_delete')
						<button type="button" class="btn btn-delete btn-sm btn-danger">
							<i class="ri-delete-bin-line"></i>
						</button>
						@endcan
					</form>
				</td>
			</tr>
			@empty
			<tr>
				<td colspan="8" class="text-center">Nada encontrado</td>
			</tr>
			@endforelse
		</tbody>
	</table>
</div>