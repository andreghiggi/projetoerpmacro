<div class="modal-header py-3 px-4 border-bottom-0">
	<h5 class="modal-title" id="modal-title">{{ $item->nome }}</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body px-4 pb-4 pt-0">
	<div class="row">
		<div class="col-md-2">
			<img style="height: 120px; border-radius: 8px;" src="{{ $item->img }}">
		</div>
		<div class="col-md-5">
			<h5>Valor de venda: <strong class="text-success">R$ {{ __moeda($item->valor_unitario) }}</strong></h5>
			<h5>Valor de compra: <strong class="text-muted">R$ {{ __moeda($item->valor_compra) }}</strong></h5>
			<h5>Código de barras: <strong class="text-muted">{{ $item->codigo_barras ?? '--' }}</strong></h5>
			<h5>Categoria: <strong class="text-muted">{{ $item->categoria ? $item->categoria->nome : '--' }}</strong></h5>
			<h5>Unidade: <strong class="text-muted">{{ $item->unidade }}</strong></h5>
			<h5>Gerenciar estoque: 
				@if($item->gerenciar_estoque)
				<span class="badge bg-success">Sim</span>
				@else
				<span class="badge bg-danger">Não</span>
				@endif
			</h5>
		</div>

		<div class="col-md-5">
			<h5>NCM: <strong class="text-muted">{{ $item->ncm }}</strong></h5>
			<h5>CEST: <strong class="text-muted">{{ $item->cest ?? '--' }}</strong></h5>
			<h5>Referência: <strong class="text-muted">{{ $item->referencia ?? '--' }}</strong></h5>
			<h5>Local de armazenamento: <strong class="text-muted">{{ $item->local_armazenamento ?? '--' }}</strong></h5>
			<h5>Marca: <strong class="text-muted">{{ $item->marca ? $item->marca->nome : '--' }}</strong></h5>
			<h5>Estoque: <strong class="text-muted">
				@if($item->estoque)
				@if(!$item->unidadeDecimal())
				{{ number_format($item->estoque->quantidade, 0, '.', '') }}
				@else
				{{ number_format($item->estoque->quantidade, 3, '.', '') }}
				@endif
				@else
				--
				@endif
			</strong></h5>
			
		</div>
	</div>

	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>CST/CSOSN</th>
					<th>CST PIS</th>
					<th>CST COFIS</th>
					<th>CST IPI</th>
					<th>% ICMS</th>
					<th>% PIS</th>
					<th>% COFINS</th>
					<th>% IPI</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{{ $item->cst_csosn }}</td>
					<td>{{ $item->cst_pis }}</td>
					<td>{{ $item->cst_cofins }}</td>
					<td>{{ $item->cst_ipi }}</td>
					<td>{{ $item->perc_icms }}</td>
					<td>{{ $item->perc_pis }}</td>
					<td>{{ $item->perc_cofins }}</td>
					<td>{{ $item->perc_ipi }}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<hr>
	<div class="col-md-12">
		<a title="Ver movimentações" href="{{ route('produtos.show', [$item->id]) }}" class="btn btn-dark btn-sm"><i class="ri-draft-line"></i> Ver movimentações</a>
		<a class="btn btn-primary btn-sm" href="{{ route('produtos.duplicar', [$item->id]) }}" title="Duplicar produto">
			<i class="ri-file-copy-line"></i> Duplicar produto
		</a>

		@if($item->composto == true)
		<a class="btn btn-info btn-sm" href="{{ route('produto-composto.show', [$item->id]) }}" title="Ver composição"><i class="ri-search-eye-fill"></i> Ver composição</a>
		@endif

		@if($item->alerta_validade != '')
		<a title="Ver lote e vencimento" type="button" class="btn btn-light btn-sm" onclick="infoVencimento('{{$item->id}}')" data-bs-toggle="modal" data-bs-target="#info_vencimento"><i class="ri-eye-line"></i> Ver lote e vencimento</a>
		@endif

		<a class="btn btn-light btn-sm" href="{{ route('produtos.etiqueta', [$item->id]) }}" title="Gerar etiqueta">
			<i class="ri-barcode-box-line"></i> Gerar etiqueta
		</a>
	</div>
</div>
<div class="modal-footer">

	<div class="text-end">
		<button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Fechar</button>
	</div>
</div>