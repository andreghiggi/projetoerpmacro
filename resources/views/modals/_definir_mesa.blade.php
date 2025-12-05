<div class="modal fade" id="definir_mesa" tabindex="-1">
	<div class="modal-dialog modal-md">
		<form method="post" action="{{ route('frontbox.definir-mesa') }}">

			<input type="hidden" id="comanda_mesa_id" name="comanda_id" value="{{ $comanda ? $comanda->id : '' }}">
			@csrf
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Mesa/Cliente</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row g-2">
						<div class="col-md-12">
							<label>Mesa</label>
							<select required name="mesa_id" class="form-control form-select">
								<option value="">Selecione</option>
								@foreach($mesas as $m)
								<option @if($comanda && $comanda->mesa_id == $m->id) selected @endif value="{{ $m->id }}">{{ $m->nome }}</option>
								@endforeach
							</select>
						</div>

						<div class="col-md-12">
							{!!Form::select('cliente_id', 'Cliente')->attrs(['class' => 'select2'])
							->options($comanda && $comanda->cliente_id ? [$comanda->cliente_id => $comanda->cliente->info] : [])
							!!}
						</div>

						<div class="col-md-12">
							{!!Form::text('cliente_nome', 'Cliente nome')
							->value($comanda ? $comanda->cliente_nome : '')
							!!}
						</div>

						<div class="col-md-12">
							{!!Form::text('cliente_fone', 'Cliente telefone')
							->value($comanda ? $comanda->cliente_fone : '')
							->attrs(['class' => 'fone'])
							!!}
						</div>
					</div>
				</div>
				<div class="modal-footer">

					<div class="text-end">
						<button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Sair</button>
						<button type="submit" class="btn btn-success">Salvar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>