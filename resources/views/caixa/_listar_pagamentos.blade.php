<div class="row">
	@foreach($somaTiposPagamento as $key => $valor)
	@if($valor > 0)
	<div class="row">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-12 col-md-6">
						<h4 class="text-success">{{ App\Models\Nfce::getTipoPagamento($key) }}</h4>
					</div>
					<div class="col-12 col-md-6">
						<h4><strong>R$ {{ __moeda($valor) }}</strong></h4>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row line-row">
					<div class="col-12 appends">
						<div class="dynamic-form row mt-4">
							<input type="hidden" value="{{ $key }}" name="tipo_pagamento[]">
							
							<div class="col-7 col-md-3">
								<label>Conta</label>
								<select required name="conta_empresa_id[]" class="form-select select2">
									<option value=""></option>
									@foreach($contasEmpresa as $c)
									<option value="{{ $c->id }}">
										{{ $c->nome }}
									</option>
									@endforeach
								</select>
							</div>

							<div class="col-5 col-md-2">
								<label>Valor</label>
								<input required type="tel" class="form-control moeda valor_linha" name="valor[]">
							</div>

							<div class="col-12 col-md-7">
								<label>Descrição</label>
								<input type="text" class="form-control ignore descricao" name="descricao[]">
							</div>

						</div>
					</div>
				</div>
				<div class="row mt-4">
					<div class="col-6 col-md-6">
						<button type="button" class="btn btn-dark btn-clone-caixa">
							<i class="ri-add-fill"></i> Adicionar linha
						</button>
					</div>
					<div class="col-6 col-md-6 text-end">
						<input type="hidden" class="valor_total" value="{{ $valor }}">
						<h5 class="float-right">Valor restante  <strong class="total-restante text-danger">R$ 0,00</strong></h5>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif
	@endforeach

	<div class="col-md-9"></div>
	<div class="col-md-3">
		<button disabled class="btn btn-success w-100 btn-store">
			Salvar
		</button>
	</div>
</div>

