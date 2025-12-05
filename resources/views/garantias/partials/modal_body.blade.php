<div class="modal-header py-3 px-4 border-bottom-0">
	<h5 class="modal-title" id="modal-title">{{ $item->produto ? $item->produto->nome : ($item->servico ? $item->servico->nome : '') }}</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body px-4 pb-4 pt-0">
	<div class="row">
		<div class="col-md-2">
			<img style="height: 120px; border-radius: 8px;" src="{{ $item->produto ? $item->produto->img : ($item->servico ? $item->servico->img : '') }}">
		</div>
		<div class="col-md-5">
			<h5>Cliente: <strong class="text-success">{{ $item->cliente->razao_social }}</strong></h5>
			<h5>CPF/CNPJ: <strong class="text-success">{{ $item->cliente->cpf_cnpj }}</strong></h5>
			<h5>Endereço: <strong class="text-success">{{ $item->cliente->endereco }}</strong></h5>
			
			{!! $item->statusFormatado() !!}
		</div>

		<div class="col-md-5">
			<h5>Data da venda: <strong class="text-muted">{{ __data_pt($item->data_venda, 0) }}</strong></h5>
			<h5>Data da solicitação: <strong class="text-muted">{{ __data_pt($item->data_solicitacao, 0) }}</strong></h5>
			<h5>Prazo de garantia: <strong class="text-muted">{{ $item->prazo_garantia }} (dias)</strong></h5>
			<h5>Data de expiração: <strong class="text-muted">{{ __data_pt($item->dataValidade(), 0) }}</strong></h5>
			
		</div>
		<hr class="mt-2">
		<div class="col-md-12">
			<label>Descrição do problema:</label><br>
			{{ $item->descricao_problema }}
		</div>

		<hr class="mt-2">
		<div class="col-md-12">
			<label>Observação:</label><br>
			{{ $item->observacao }}
		</div>
	</div>

	
	
	</div>
</div>
<div class="modal-footer">

	<div class="text-end">
		<button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Fechar</button>
	</div>
</div>