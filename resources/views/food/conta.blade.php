@extends('food.default', ['title' => 'Minha conta'])
@section('content')

@section('css')
<style type="text/css">
	.image{
		height: 50px;
	}

	input[type="checkbox"] {
		/* ...existing styles */
		display: grid;
		place-content: center;
	}

	input[type="checkbox"]::before {
		content: "";
		width: 0.65em;
		height: 0.65em;
		transform: scale(0);
		transition: 120ms transform ease-in-out;
		box-shadow: inset 1em 1em var(--form-control-color);
	}

	input[type="checkbox"]:checked::before {
		transform: scale(1);
	}

	.check{
		margin-top: 30px;
	}

	/* ======= Estilo geral da tabela ======= */
	.table {
		width: 100%;
		border-collapse: collapse;
		margin-bottom: 20px;
		border-radius: 12px;
		overflow: hidden;
		background: #fff;
		box-shadow: 0 2px 6px rgba(0,0,0,0.08);
	}

	.table th, 
	.table td {
		padding: 10px 12px;
		text-align: left;
		vertical-align: middle;
		border-bottom: 1px solid #eee;
	}

	.table th {
		background: #f9fafb;
		font-weight: 600;
		color: #111;
		font-size: 14px;
	}

	.table img {
		width: 60px;
		height: 60px;
		border-radius: 8px;
		object-fit: cover;
	}

	@media (max-width: 768px) {
		.table, 
		.table thead, 
		.table tbody, 
		.table th, 
		.table td, 
		.table tr {
			display: block;
			width: 100%;
		}

		.table thead {
			display: none;
		}

		.table tr {
			background: #fff;
			border: 1px solid #eee;
			border-radius: 12px;
			box-shadow: 0 2px 8px rgba(0,0,0,0.06);
			margin-bottom: 14px;
			padding: 12px;
		}

		.table td {
			border: none;
			padding: 6px 0;
			position: relative;
		}

		.table td::before {
			content: attr(data-label);
			font-weight: 600;
			color: #6b7280;
			display: block;
			font-size: 13px;
			margin-bottom: 4px;
		}

		.table img {
			width: 80px;
			height: 80px;
			margin-bottom: 8px;
		}
	}

	.card-footer {
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-wrap: wrap;
		gap: 10px;
		padding-top: 10px;
		border-top: 1px solid #eee;
	}

	.btn-success {
		background: #22c55e !important;
		color: #fff !important;
		border: none;
		border-radius: 8px;
		padding: 10px 16px;
		font-weight: 600;
		transition: 0.2s ease;
	}

	.btn-success:hover {
		background: #16a34a !important;
	}

</style>
@endsection

<div class="minfit" style="min-height: 472px;">
	<div class="middle">
		<div class="container nopaddmobile">
			<div class="row rowtitle">
				<div class="col-md-12">
					<div class="title-icon">
						<span style="font-size: 18px;">{{ $cliente->razao_social }}</span>
					</div>
					<div class="bread-box">
						<div class="bread">
							<a href="{{ route('food.index', ['link='.$config->loja_id]) }}"><i class="lni lni-home"></i></a>
							<span>/</span>
							<a>Minha conta</a>
						</div>
					</div>
				</div>
			</div>

			<div class="row" style="margin-top: 20px;">

				@foreach($cliente->enderecos as $e)
				<div class="col-md-4 col-infinite">
					<div class="novoproduto" style="box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px; height: 160px">

						<h5>{{ $e->info }}<br>{{ $e->cep }}</h5>
						@if($e->padrao)
						<p class="text-danger">endereço padrão</p>
						@endif
						
						<form action="{{ route('food.remove-endereco', [$e->id, 'link='.$config->loja_id]) }}" method="post" id="form-{{$e->id}}">
							@csrf
							@method('delete')
							<button type="button" onclick="editEndereco('{{ json_encode($e) }}')" class="sacola-adicionar botao-acao btn-sm" style="position: absolute; top: 110px; width: 100px;">
								<i class="lni lni-pencil-alt"></i><span>Editar</span>
							</button>
							<button type="button" class="sacola-adicionar botao-acao btn-delete btn-sm" style="position: absolute; top: 110px; width: 110px; right: 30px; background-color: #C9302C !important;">
								<i class="lni lni-trash"></i><span>Remover</span>
							</button>
						</form>
					</div>
				</div>
				@endforeach

			</div>

			<div class="container" style="margin-top: 10px;">
				<h4>Pedidos</h4>

				@foreach($cliente->pedidos as $p)
				<div class="card mt-2">
					<div class="card-header">
						<div class="row" style="font-size: 18px; margin-top: 10px;">
							<div class="col-md-4 col-12">
								#{{ $p->id }} - 
								@if($p->estado == 'novo')
								<span class="text-primary">Novo</span>
								@elseif($p->estado == 'aprovado')
								<span class="text-success">Aprovado</span>
								@elseif($p->estado == 'cancelado')
								<span class="text-danger">Cancelado</span>
								@else
								<span class="text-main">Finalizado</span>
								@endif
							</div>
							<div class="col-md-4 col-12">
								{{ __data_pt($p->created_at) }}
							</div>
							<div class="col-md-4 col-12">
								Valor total do pedido: <strong>R${{ __moeda($p->valor_total) }}</strong>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th>#</th>
										<th>Produto</th>
										<th>Qtd.</th>
										<th>Valor unitário</th>
										<th>Sub total</th>
									</tr>
								</thead>
								<tbody>
									@foreach($p->itens as $i)
									<tr>
										<td>
											<img class="image" src="{{ $i->produto->img }}">
										</td>
										<td data-label="Produto">
											@if($i->tamanho)
											@foreach($i->pizzas as $pizza)
											1/{{ sizeof($i->pizzas) }} {{ $pizza->sabor->nome }} @if(!$loop->last) | @endif
											@endforeach
											- Tamanho: <strong>{{ $i->tamanho->nome }}</strong>
											@else
											{{ $i->produto->nome }}
											@endif
										</td>
										<td data-label="Quantidade">{{ number_format($i->quantidade, 0) }}</td>
										<td data-label="Valor unitário">{{ __moeda($i->valor_unitario) }}</td>
										<td data-label="Sub total">{{ __moeda($i->sub_total) }}</td>
									</tr>
									@if(sizeof($i->adicionais) > 0)
									<tr>
										<td colspan="5">
											Adicionais: 
											@foreach($i->adicionais as $a)
											<strong>{{ $a->adicional->nome }}@if(!$loop->last), @endif</strong>
											@endforeach
										</td>
									</tr>
									@endif

									@if($i->observacao)
									<tr>
										<td colspan="5">
											Observação: 
											<strong>{{ $i->observacao }}</strong>
										</td>
									</tr>
									@endif
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					<div class="card-footer">
						<div class="row">
							<div class="col-md-4 col-12">
								Valor de entrega: <strong>R${{ __moeda($p->valor_entrega) }}</strong><br>
								Forma de pagamengo: <strong>{{ $p->tipo_pagamento }}</strong>
							</div>

							<div class="col-md-4 col-12">
								Desconto: <strong>R${{ __moeda($p->desconto) }}</strong>
								@if($p->cupom)#{{ $p->cupom->codigo }}@endif
							</div>

							<div class="col-md-4 col-12">
								<a href="{{ route('food.carrinho-pedir-novamente', [$p->id, 'link='.$config->loja_id]) }}" class="btn btn-success">
									Pedir novamente
								</a>
							</div>
						</div>
					</div>
				</div>
				<hr>
				@endforeach
			</div>
		</div>
	</div>
</div>

@include('food.partials.modal_edit_endereco')

@endsection

@section('js')
<script type="text/javascript" src="/delivery/js/cart.js"></script>
@endsection