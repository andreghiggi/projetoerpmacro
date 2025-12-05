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
			<h5>Razão social: <strong class="text-success">{{ $item->razao_social }}</strong></h5>
			<h5>Nome Fantasia: <strong class="text-muted">{{ $item->nome_fantasia }}</strong></h5>
			<h5>CPF/CNPJ: <strong class="text-muted">{{ $item->cpf_cnpj }}</strong></h5>
			<h5>I.E: <strong class="text-muted">{{ $item->ie }}</strong></h5>
			<h5>Status: 
				@if($item->status) 
				<span class="badge bg-success">Ativo</span>
				@else
				<span class="badge bg-danger">Desativado</span>
				@endif
			</h5>
			<h5>Consumidor final: 
				@if($item->consumidor_final) 
				<span class="badge bg-success">Sim</span>
				@else
				<span class="badge bg-danger">Não</span>
				@endif
			</h5>
			<h5>Contribuinte: 
				@if($item->contribuinte) 
				<span class="badge bg-success">Sim</span>
				@else
				<span class="badge bg-danger">Não</span>
				@endif
			</h5>
			
		</div>

		<div class="col-md-5">
			<h5>Rua: <strong class="text-muted">{{ $item->rua }}</strong></h5>
			<h5>Bairro: <strong class="text-muted">{{ $item->bairro }}</strong></h5>
			<h5>CEP: <strong class="text-muted">{{ $item->cep }}</strong></h5>
			<h5>Cidade: <strong class="text-muted">{{ $item->cidade ? $item->cidade->info : '--' }}</strong></h5>
			<h5>Complemento: <strong class="text-muted">{{ $item->complemento ?? '--' }}</strong></h5>
			<h5>Telefone: <strong class="text-muted">{{ $item->telefone }}</strong></h5>
			<h5>Data de nascimento: <strong class="text-muted">{{ $item->data_nascimento ?? '--' }}</strong></h5>
			
		</div>
	</div>

	
	<hr>
	<div class="col-md-12">
		<a title="Informações de cashBack" class="btn btn-dark btn-sm" href="{{ route('clientes.cash-back', [$item->id]) }}">
			<i class="ri-coins-fill"></i> Informações de cashBack
		</a>

		<a title="Histórico" class="btn btn-primary btn-sm" href="{{ route('clientes.historico', [$item->id]) }}">
			<i class="ri-file-list-3-fill"></i> Histórico
		</a>

		@can('crm_create')
		<button type="button" title="CRM" class="btn btn-light btn-sm" onclick="modalCrm('{{ $item->id }}')">
			<i class="ri-user-voice-fill"></i> CRM
		</button>
		@endcan
	</div>
</div>
<div class="modal-footer">

	<div class="text-end">
		<button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Fechar</button>
	</div>
</div>