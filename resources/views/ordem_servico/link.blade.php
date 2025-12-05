<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<title>Acompanhamento de OS - {{ $empresa->nome }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
	<style>
		body { background-color: #f8f9fa; font-family: 'Nunito', sans-serif; }
		.card-os { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-top: 20px; }
		.status-badge { font-size: 14px; padding: 6px 12px; border-radius: 20px; font-weight: 600; }
		.table th { background-color: #343a40; color: #fff; }
		.navbar-brand img { max-height: 60px; }
		.valor { font-weight: bold; color: #198754; }


		@media (max-width: 768px) {
			table.table thead {
				display: none !important;
			}

			table.table,
			table.table tbody,
			table.table tr,
			table.table td {
				display: block !important;
				width: 100% !important;
			}

			table.table tr {
				background-color: #fff;
				border: 1px solid #dee2e6;
				border-radius: 10px;
				margin-bottom: 1rem;
				box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
				padding: 10px;
			}

			table.table td {
				text-align: right !important;
				padding: 8px 12px;
				border: none !important;
				position: relative;
			}

			table.table td::before {
				content: attr(data-label);
				position: absolute;
				left: 12px;
				width: 50%;
				font-weight: 600;
				text-align: left;
				color: #555;
			}

			table.table td:last-child {
				border-bottom: none !important;
			}
		}

	</style>
</head>
<body>

	<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
		<div class="container">
			<a class="navbar-brand d-flex align-items-center gap-2">
				@if($empresa->logo)
				<img src="{{ $empresa->img }}" width="120">
				@else
				<strong>{{ $empresa->nome }}</strong>
				@endif
			</a>
		</div>
	</nav>

	<main class="container py-4">
		<div class="card card-os">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="mb-0 text-primary">Ordem de Serviço #{{ $ordem->codigo_sequencial }}</h4>
					@php
					$estado = [
					'pd' => ['Pendente', 'warning'],
					'ap' => ['Aprovada', 'success'],
					'rp' => ['Reprovada', 'danger'],
					'fz' => ['Finalizada', 'info']
					][$ordem->estado];
					@endphp
					<span class="status-badge bg-{{ $estado[1] }}">{{ $estado[0] }}</span>
				</div>

				<div class="row mb-3">
					<div class="col-md-3 col-6"><strong>Cliente:</strong><br>{{ $ordem->cliente->info ?? '--' }}</div>
					<div class="col-md-3 col-6"><strong>Data início:</strong><br>{{ __data_pt($ordem->data_inicio) }}</div>
					<div class="col-md-3 col-6"><strong>Entrega:</strong><br>{{ $ordem->data_entrega ? __data_pt($ordem->data_entrega) : '--' }}</div>
					<div class="col-md-3 col-6"><strong>Valor Total:</strong><br><span class="valor">R$ {{ __moeda($ordem->valor) }}</span></div>
				</div>

				@if($ordem->equipamento != null)
				
				<div class="row mb-3">
					<div class="col-md-3 col-6"><strong>Equipamento:</strong><br>{{ $ordem->equipamento }}</div>
					<div class="col-md-3 col-6"><strong>Número de série:</strong><br>{{ $ordem->numero_serie ?? '--' }}</div>
					<div class="col-md-3 col-6"><strong>Cor:</strong><br>{{ $ordem->cor ?? '--' }}</div>
				</div>

				<div class="row mb-3">
					<strong>Diagnóstico técnico:</strong>
					{!! $ordem->diagnostico_tecnico !!}
				</div>
				@endif

				@if($ordem->descricao)
				<div class="mb-3">
					<h6 class="fw-bold">Descrição</h6>
					<div class="border rounded p-2 bg-light">{!! $ordem->descricao !!}</div>
				</div>
				@endif

				<h5 class="mt-4"><i class="ri-shopping-bag-2-line"></i> Produtos</h5>
				<div class="table-responsive">
					<table class="table table-striped table-bordered mt-2">
						<thead>
							<tr>
								<th>Produto</th>
								<th>Quantidade</th>
								<th>Valor Unitário</th>
								<th>Subtotal</th>
							</tr>
						</thead>
						<tbody>
							@forelse($ordem->itens as $item)
							<tr>
								<td data-label="Produto">{{ $item->produto->nome ?? '--' }}</td>
								<td data-label="Quantidade">{{ $item->quantidade }}</td>
								<td data-label="Valor Unitário">R$ {{ __moeda($item->valor) }}</td>
								<td data-label="Subtotal"><strong>R$ {{ __moeda($item->subtotal) }}</strong></td>
							</tr>
							@empty
							<tr><td colspan="4" class="text-center text-muted">Nenhum produto vinculado</td></tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<h5 class="mt-4"><i class="ri-tools-line"></i> Serviços</h5>
				<div class="table-responsive">
					<table class="table table-striped table-bordered mt-2">
						<thead>
							<tr>
								<th>Serviço</th>
								<th>Quantidade</th>
								<th>Status</th>
								<th>Subtotal</th>
							</tr>
						</thead>
						<tbody>
							@forelse($ordem->servicos as $serv)
							<tr>
								<td data-label="Serviço">{{ $serv->servico->nome ?? '--' }}</td>
								<td data-label="Quantidade">{{ $serv->quantidade }}</td>
								<td data-label="Status">
									@if($serv->status)
									<span class="badge bg-success">Finalizado</span>
									@else
									<span class="badge bg-warning text-dark">Pendente</span>
									@endif
								</td>
								<td data-label="Subtotal"><strong>R$ {{ __moeda($serv->subtotal) }}</strong></td>
							</tr>
							@empty
							<tr><td colspan="4" class="text-center text-muted">Nenhum serviço vinculado</td></tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($ordem->relatorios->count() > 0)
				<h5 class="mt-4"><i class="ri-file-list-line"></i> Relatórios</h5>
				<ul class="list-group mt-2">
					@foreach($ordem->relatorios as $rel)
					<li class="list-group-item d-flex justify-content-between align-items-center">
						{{ __data_pt($rel->created_at) }}

						<button class="btn btn-sm btn-outline-primary" onclick="modalRelatorio('{{ $rel->texto }}')">
							<i class="ri-eye-line"></i> Ver
						</button>
					</li>
					@endforeach
				</ul>
				@endif

			</div>
		</div>
	</main>

	<footer class="text-center text-muted mt-4 mb-3">
		<small>© {{ date('Y') }} {{ $empresa->nome }} — Todos os direitos reservados</small>
	</footer>

	<div class="modal fade" id="modal-relatorio" tabindex="-1" aria-labelledby="" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header bg-dark text-white">
					<h5 class="modal-title">Relatório</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
				</div>
				<div class="modal-body">

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
				</div>
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript">
		function modalRelatorio(texto) {
			$('#modal-relatorio').modal('show')
			$('#modal-relatorio .modal-body').html(texto)
		}
	</script>
</body>
</html>
