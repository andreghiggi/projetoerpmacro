<div class="row">

	<div class="col-md-4">
		<span class="text-primary">Data de cadastro:</span> <strong class="text-muted">{{ __data_pt($item->created_at) }}</strong>
	</div>

	<div class="col-md-4">
		<span class="text-primary">Data de agendamento:</span> <strong class="text-muted">{{ __data_pt($item->agendar_para, 0) }}</strong>
	</div>

	<div class="col-md-4">
		<span class="text-primary">Data de envio:</span> <strong class="text-muted">{{ $item->status == 'enviado' ? __data_pt($item->enviado_em) : '--' }}</strong>
	</div>

	<div class="col-md-4">
		<span class="text-primary">Status:</span> 
		@if($item->status == 'pendente')
		<span class="badge bg-warning">PENDENTE</span>
		@elseif($item->status == 'enviado')
		<span class="badge bg-success">ENVIADO</span>
		@else
		<span class="badge bg-danger">ERRO</span>
		@endif
	</div>

	@if($item->enviar_whatsapp)
	<div class="col-md-4">
		<span class="text-primary">WhatsApp:</span> <strong class="text-muted">{{ $item->whatsapp }}</strong>
	</div>
	@endif

	@if($item->enviar_email)
	<div class="col-md-4">
		<span class="text-primary">Email:</span> <strong class="text-muted">{{ $item->email }}</strong>
	</div>
	@endif

	@if($item->cliente)
	<div class="col-md-6">
		<span class="text-primary">Cliente:</span> <strong class="text-muted">{{ $item->cliente->info }}</strong>
	</div>
	@endif

	<div class="col-md-12">
		<br>
		{{ $item->mensagem }}
	</div>

	@if($item->status == 'erro')
	<div class="col-md-12">
		<span class="text-danger">Erro</span><br>
		{{ $item->erro }}
	</div>
	@endif

</div>