<div class="row">
	<div class="col-md-6 col-12">
		<h5>Data de abertura: <strong>{{ __data_pt($ticket->created_at, 1) }}</strong></h5>
	</div>
	<div class="col-md-6 col-12">
		<h5>Departamento: <strong>{{ $ticket->departamento }}</strong></h5>
	</div>

	<div class="col-md-2 col-6">
		<a class="btn btn-dark" href="{{ route('ticket-super.show', [$ticket->id]) }}">Ver ticket</a>
	</div>
</div>