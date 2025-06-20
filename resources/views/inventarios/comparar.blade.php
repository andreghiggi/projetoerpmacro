@extends('layouts.app', ['title' => 'Inventário ' . $item->numero_sequencial])
@section('css')
<style type="text/css">
	@page { size: auto;  margin: 0mm; }

	@media print {
		.print{
			margin: 10px;
		}
	}
</style>
@endsection
@section('content')
<div class="card mt-1 print">
	<div class="card-body">
		<div class="pl-lg-4">

			<div class="ms">

				<div class="mt-3 d-print-none" style="text-align: right;">
					<a href="{{ route('inventarios.apontar', [$item->id]) }}" class="btn btn-danger btn-sm px-3">
						<i class="ri-arrow-left-double-fill"></i>Voltar
					</a>
				</div>

				<div class="row mb-2">

					<div class="col-md-3 col-6">
						<h5><strong class="text-danger">#{{ $item->numero_sequencial }}</strong></h5>
					</div>
					<div class="col-md-3 col-6">
						<h5>Data de Criação: <strong class="text-primary">{{ __data_pt($item->created_at) }}</strong></h5>
					</div>

					<div class="col-md-3 col-6">
						<h5>Data de Início: <strong class="text-primary">{{ __data_pt($item->inicio, 0) }}</strong></h5>
					</div>

					<div class="col-md-3 col-6">
						<h5>Data de Fim: <strong class="text-primary">{{ __data_pt($item->fim, 0) }}</strong></h5>
					</div>


					<div class="col-md-3 col-6">
						<h5>Itens aprontados: <strong class="text-primary">{{ sizeof($item->itens) }}</strong> </h5>
					</div>

					<div class="col-md-3 col-6">
						<h5>Itens com diferença: <strong class="text-primary">{{ $itensDiferentes }}</strong> </h5>
					</div>

					<div class="col-md-3 col-6">
						<h5>Usuário: <strong class="text-primary">{{ $item->usuario->name }}</strong> </h5>
					</div>

					<div class="col-md-6 col-12">
						<h5>Observação: <strong class="text-primary">{{ $item->observação ?? '--' }}</strong> </h5>
					</div>

				</div>

				<a class="btn btn-primary btn-sm d-print-none" href="javascript:window.print()" ><i class="ri-printer-line d-print-none"></i>
					Imprimir
				</a>
			</div>

			<div class="row mt-2">
				<h4>Itens Apontados</h4>
				<h5><i class="ri-flag-2-fill text-danger"></i> Itens em vermelho com difereça de apontamento</h4>
					<div class="table-responsive-sm">
						<table class="table table-striped table-centered mb-0">
							<thead class="table-dark">
								<tr>
									<th>Produto</th>
									<th>Quantidade apontada</th>
									<th>Quantidade em estoque</th>
									<th>Diferença</th>
									<th>Estado</th>
									<th class="d-print-none">Ações</th>
								</tr>
							</thead>
							<tbody>
								@foreach($data as $i)
								<tr>
									<td @if($i['diferenca'] != 0) class="text-danger" @endif>{{ $i['nome'] }}</td>
									<td>{{ $i['quantidade'] }}</td>
									<td>{{ $i['estoque'] }}</td>
									<td>{{ $i['diferenca'] }}</td>

									<td>{{ $i['estado'] }}</td>
									<td class="d-print-none">
										<form action="{{ route('inventarios.destroy-item', $i['id']) }}" method="post" id="form-{{$i['id']}}">
											@method('delete')
											@csrf
											<button type="button" class="btn btn-delete btn-sm btn-danger">
												<i class="ri-delete-bin-line"></i>
											</button>

										</form>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				<br>
				<p class="d-print-none text-danger">
					<i class="ri-alert-line"></i>
					Atenção, se o estoque for definido a partir deste inventário as quantidades anteriores não poderam ser retornadas!
				</p>
				<a class="btn btn-danger btn-sm d-print-none" href="{{ route('inventarios.definir-estoque', [$item->id]) }}">
					<i class="ri-check-line"></i>
					Definir estoque
				</a>
			</div>
		</div>
	</div>
	@endsection