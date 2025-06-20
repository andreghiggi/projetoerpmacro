@extends('layouts.app', ['title' => 'Itens inventário ' . $item->numero_sequencial])
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
					<a href="{{ route('inventarios.index') }}" class="btn btn-danger btn-sm px-3">
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
				<div class="table-responsive-sm">
					<table class="table table-striped table-centered mb-0">
						<thead class="table-dark">
							<tr>
								<th>Produto</th>
								<th>Quantidade</th>
								<th>Valor de venda</th>
								<th>Valor de compra</th>
								<th>Estado</th>
							</tr>
						</thead>
						<tbody>
							@foreach($item->itens as $i)
							<tr>
								<td>{{ $i->produto->nome }}</td>
								<td>{{ number_format($i->quantidade, 0) }}</td>
								<td>{{ __moeda($i->produto->valor_unitario) }}</td>
								<td>{{ __moeda($i->produto->valor_compra) }}</td>
								<td>{{ $i->estado }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<br>
			<a class="btn btn-dark btn-sm d-print-none" href="{{ route('inventarios.comparar-estoque', [$item->id]) }}">
				<i class="ri-arrow-left-right-line"></i>
				Comparar estoque
			</a>
		</div>
	</div>
</div>
@endsection