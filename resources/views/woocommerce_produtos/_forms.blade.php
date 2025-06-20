<div class="row g-2">

	<div class="col-md-3">
		{!!Form::select('padrao_id', 'Padrão de tributação', ['' => 'Selecione'] + $padroes->pluck('descricao', 'id')->all())
		->attrs(['class' => 'form-select'])
		->value(isset($item) ? $item->padrao_id : ($padraoTributacao != null ? $padraoTributacao->id : ''))
		!!}
	</div>

	@if(__countLocalAtivo() > 1)
	<div class="col-md-4">
		<label for="">Disponibilidade</label>

		<select required class="select2 form-control select2-multiple" data-toggle="select2" name="locais[]" multiple="multiple">
			@foreach(__getLocaisAtivoUsuario() as $local)
			<option @if(in_array($local->id, (isset($item) ? $item->locais->pluck('localizacao_id')->toArray() : []))) selected @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
			@endforeach
		</select>
	</div>
	@else
	<input type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
	@endif

	<div class="table-responsive">
		<table class="table table-striped table-centered mb-0">
			<thead class="table-dark">
				<tr>
					<td>Nome</td>
					<td>Status</td>
					<td>ID plataforma</td>
					<td>Slug</td>
					<td>Valor de venda</td>
					<td>Valor de venda plataforma</td>
					<td>Estoque</td>
					<td>Valor de compra</td>
					<td>Código de barras</td>
					<td>Categoria</td>
					<td>Unidade</td>
					<td>Gerencia estoque</td>
					<td>NCM</td>
					<td>CEST</td>
					<td>CFOP sáida estadual</td>
					<td>CFOP sáida outro estado</td>

					<td>CFOP entrada estadual</td>
					<td>CFOP entrada outro estado</td>
					<td>%ICMS</td>
					<td>%PIS</td>
					<td>%COFINS</td>
					<td>%IPI</td>
					<td>%RED. BC</td>
					<td>CST/CSOSN</td>
					<td>CST/PIS</td>
					<td>CST/COFINS</td>
					<td>CST/IPI</td>
					<td>Código de enquandramento de IPI</td>
				</tr>
			</thead>
			<tbody>
				@foreach($produtosIsert as $p)
				<tr>
					<input type="hidden" class="form-control" name="woocommerce_link[]" value="{{ $p['woocommerce_link'] }}">
					<input type="hidden" class="form-control" name="woocommerce_stock_status[]" value="{{ $p['woocommerce_stock_status'] }}">
					<input type="hidden" class="form-control" name="woocommerce_descricao[]" value="{{ $p['woocommerce_descricao'] }}">
					<input type="hidden" class="form-control" name="categorias_woocommerce[]" value="{{ $p['categorias_woocommerce'] }}">
					<input type="hidden" class="form-control" name="woocommerce_type[]" value="{{ $p['woocommerce_type'] }}">
					<td>
						<input readonly required style="width: 400px" type="" class="form-control" name="nome[]" value="{{ $p['nome'] }}">

					</td>
					<td>
						<input style="width: 150px" readonly type="" class="form-control" name="woocommerce_status[]" value="{{ $p['woocommerce_status'] }}">
					</td>
					<td>
						<input style="width: 150px" readonly type="" class="form-control" name="woocommerce_id[]" value="{{ $p['woocommerce_id'] }}">
					</td>
					<td>
						<input style="width: 150px" readonly type="" class="form-control" name="woocommerce_slug[]" value="{{ $p['woocommerce_slug'] }}">
					</td>
					<td>
						<input required style="width: 200px" type="tel" class="form-control moeda" name="valor_venda[]" value="{{ __moeda($p['valor_venda']) }}">
					</td>
					<td>
						<input required style="width: 200px" type="tel" class="form-control moeda" name="woocommerce_valor[]" value="{{ __moeda($p['woocommerce_valor']) }}">
					</td>

					<td>
						<input required style="width: 120px" type="tel" class="form-control" name="estoque[]" value="{{ __moeda($p['estoque']) }}" data-mask='00000.00' data-mask-reverse="true">
					</td>
					<td>
						<input style="width: 200px" type="tel" class="form-control moeda" name="valor_compra[]" value="">
					</td>
					<td>
						<input style="width: 200px" type="tel" class="form-control" name="codigo_barras[]" value="">
					</td>
					<td>
						<select style="width: 250px" class="form-select" name="categoria_id[]">
							<option value=""></option>
							@foreach($categorias as $c)
							<option value="{{ $c->id }}">{{ $c->nome }}</option>
							@endforeach
						</select>
					</td>

					<td>
						<select required style="width: 130px" class="form-select" required name="unidade[]">
							@foreach($unidades as $un)
							<option @if($un == 'UN') selected @endif value="{{ $un->nome }}">{{ $un->nome }}</option>
							@endforeach
						</select>
					</td>
					

					<td>
						<select style="width: 130px" class="form-select" required name="gerenciar_estoque[]">
							<option value="1">Sim</option>
							<option value="0">Não</option>
						</select>
					</td>
					<td>
						<input required style="width: 150px" type="tel" class="form-control ncm" name="ncm[]" value="">
					</td>
					<td>
						<input style="width: 150px" type="tel" class="form-control cest" name="cest[]" value="">
					</td>
					<td>
						<input required style="width: 150px" type="tel" class="form-control cfop cfop_estadual" name="cfop_estadual[]" value="">
					</td>
					<td>
						<input required style="width: 150px" type="tel" class="form-control cfop cfop_outro_estado" name="cfop_outro_estado[]" value="">
					</td>

					<td>
						<input required style="width: 150px" type="tel" class="form-control cfop cfop_entrada_estadual" name="cfop_entrada_estadual[]" value="">
					</td>
					<td>
						<input required style="width: 150px" type="tel" class="form-control cfop cfop_entrada_outro_estado" name="cfop_entrada_outro_estado[]" value="">
					</td>

					<td>
						<input required style="width: 120px" type="tel" class="form-control percentual perc_icms" name="perc_icms[]" value="">
					</td>
					<td>
						<input required style="width: 120px" type="tel" class="form-control percentual perc_pis" name="perc_pis[]" value="">
					</td>
					<td>
						<input required style="width: 120px" type="tel" class="form-control percentual perc_cofins" name="perc_cofins[]" value="">
					</td>
					<td>
						<input required style="width: 120px" type="tel" class="form-control percentual perc_ipi" name="perc_ipi[]" value="">
					</td>
					<td>
						<input style="width: 120px" type="tel" class="form-control percentual perc_red_bc" name="perc_red_bc[]" value="">
					</td>
					<td>
						<select style="width: 350px" class="form-select cst_csosn" required name="cst_csosn[]">
							<option value=""></option>
							@foreach($listaCTSCSOSN as $key => $l)
							<option value="{{ $key }}">{{ $l }}</option>
							@endforeach
						</select>
					</td>

					<td>
						<select style="width: 300px" class="form-select cst_pis" required name="cst_pis[]">
							<option value=""></option>
							@foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $l)
							<option value="{{ $key }}">{{ $l }}</option>
							@endforeach
						</select>
					</td>

					<td>
						<select style="width: 300px" class="form-select cst_cofins" required name="cst_cofins[]">
							<option value=""></option>
							@foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $l)
							<option value="{{ $key }}">{{ $l }}</option>
							@endforeach
						</select>
					</td>
					<td>
						<select style="width: 300px" class="form-select cst_ipi" required name="cst_ipi[]">
							<option value=""></option>
							@foreach(App\Models\Produto::listaCST_IPI() as $key => $l)
							<option value="{{ $key }}">{{ $l }}</option>
							@endforeach
						</select>
					</td>
					<td>
						<select style="width: 500px" class="form-select cEnq" required name="cEnq[]">
							<option value=""></option>
							@foreach(App\Models\Produto::listaCenqIPI() as $key => $l)
							<option value="{{ $key }}">{{ $l }}</option>
							@endforeach
						</select>
					</td>
				</tr>

				@if(isset($p['variacoes']) && sizeof($p['variacoes']) > 1)
				<tr>
					<td colspan="5">
						<table class="table">
							<thead>
								<tr>
									<th>Tipo</th>
									<th>Variação</th>
									<th>Valor</th>
									<th>Quantidade</th>
									<th>ID variação</th>
									<th>Código de barras</th>
								</tr>
							</thead>
							<tbody>
								@foreach($p['variacoes'] as $v)
								<tr>
									<td>
										<input readonly class="form-control" type="" value="{{ $v['nome'] }}" name="variacao_tipo[]">
									</td>
									<td>
										<input readonly class="form-control" type="" value="{{ $v['valor_nome'] }}" name="variacao_nome[]">
									</td>
									<td>
										<input readonly class="form-control moeda" type="tel" value="{{ __moeda($v['valor']) }}" name="variacao_valor[]">
									</td>

									<td>
										<input readonly class="form-control " type="tel" value="{{ ($v['quantidade']) }}" name="variacao_quantidade[]">
									</td>

									<td>
										<input readonly class="form-control" type="" value="{{ $v['_id'] }}" name="variacao_id[]">
									</td>
									<td>
										<input readonly class="form-control" type="" value="{{ $v['codigo_barras'] }}" name="variacao_codigo_barras[]">
									</td>
									<input type="hidden" name="woocommerce_id_row[]" value="{{ $p['woocommerce_id'] }}">

								</tr>
								@endforeach
							</tbody>
						</table>
					</td>
				</tr>
				@endif

				@endforeach
			</tbody>
		</table>
	</div>
	

	<div class="col-12" style="text-align: right;">
		<button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
	</div>
</div>