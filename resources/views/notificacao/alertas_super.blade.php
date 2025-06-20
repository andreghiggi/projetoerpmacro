
@foreach($notificacoes as $n)
<a href="{{ route('notificacao-super.show', [$n->id]) }}" class="dropdown-item p-0 notify-item unread-noti card m-0 shadow-none">
	<div class="card-body">
		<div class="d-flex align-items-center">
			@if($n->prioridade == 'baixa')
			<div class="flex-shrink-0">
				<div class="notify-icon bg-primary">
					<i class="ri-error-warning-line fs-20"></i>
				</div>
			</div>
			@elseif($n->prioridade == 'media')
			<div class="flex-shrink-0">
				<div class="notify-icon bg-warning">
					<i class="ri-alert-line fs-20"></i>
				</div>
			</div>
			@else
			<div class="flex-shrink-0">
				<div class="notify-icon bg-danger">
					<i class="ri-spam-2-fill fs-20"></i>
				</div>
			</div>
			@endif
			<div class="flex-grow-1 text-truncate ms-2">
				<h5 class="noti-item-title fw-medium fs-14">
					{{ $n->titulo }}					
					<small class="fw-normal text-muted float-end ms-1">
						{{ __hora_pt($n->created_at) }}
					</small>
				</h5>
				<small class="noti-item-subtitle text-muted">
					{{ $n->descricao_curta }}
				</small>
			</div>
		</div>
	</div>
</a>
@endforeach