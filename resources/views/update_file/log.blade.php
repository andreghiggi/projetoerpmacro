@extends('layouts.app', ['title' => 'Histórico de Atualizações'])
@section('content')
<div class="mt-3">
	<div class="card">
		<div class="card-header">
			<div style="text-align: right; margin-top: -5px;">

				<a href="{{ route('update-file.index') }}" class="btn btn-danger btn-sm px-3">
					<i class="ri-arrow-left-double-fill"></i>Voltar
				</a>
			</div>
		</div>
		<div class="card-body">

			<div class="table-responsive">
				<table class="table table-striped table-centered mb-0">
					<thead class="table-dark">
						<tr>
							<th>Data</th>
							<th>Versão</th>
						</tr>
					</thead>
					<tbody>
						@foreach($data as $item)
						<tr>
							<td>{{ __data_pt($item->created_at) }}</td>
							<td>{{ $item->versao }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

		</div>
	</div>
</div>
@endsection
