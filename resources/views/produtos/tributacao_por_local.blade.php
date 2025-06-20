@extends('layouts.app', ['title' => 'Valores por Local'])
@section('content')

<div class="card mt-1">
	<div class="card-header">
		<h4>Defina os valores por localização - <strong>{{ $item->nome }}</strong></h4>
		<div style="text-align: right; margin-top: -35px;">
			<a href="{{ route('produtos.index') }}" class="btn btn-danger btn-sm px-3">
				<i class="ri-arrow-left-double-fill"></i>Voltar
			</a>
		</div>
	</div>
	<div class="card-body">
		{!!Form::open()
		->put()
		->route('produto-tributacao-local.update', [$item->id])
		!!}
		<div class="pl-lg-4">

			<div id="basicwizard">
				<ul class="nav nav-pills nav-justified form-wizard-header mb-4 m-2">
					@foreach($locais as $key => $l)
					<li class="nav-item">
						<a href="#tab-{{ $key }}" data-bs-toggle="tab" data-toggle="tab"  class="nav-link rounded-0 py-1"> 
							<i class="ri-map-pin-line fw-normal fs-18 align-middle me-1"></i>
							<span class="d-none d-sm-inline">{{ $l->descricao }}</span>
						</a>
					</li>
					@endforeach
				</ul>

				@foreach($locais as $key => $l)
				<div class="tab-content b-0 mb-0">
					<div class="tab-pane" id="tab-{{ $key }}">
						<div class="row g-2 m-2">
							<input type="hidden" name="local_id[]" value="{{ $l->id }}">
							<div class="col-md-2">
								{!!Form::tel('valor_unitario[]', 'Valor de venda')
								->required()
								->value(__moeda(__tributacaoProdutoLocal($item, 'valor_unitario', $l->id)))
								->attrs(['class' => 'moeda'])
								!!}
							</div>

							<div class="col-md-2">
								{!!Form::tel('ncm[]', 'NCM')
								->required()
								->value(__tributacaoProdutoLocal($item, 'ncm', $l->id))
								->attrs(['class' => 'ncm'])
								->id('ncm-temp')
								->required(__isPlanoFiscal())
								!!}
							</div>

							<div class="col-md-2">
								{!!Form::tel('cest[]', 'CEST')
								->attrs(['class' => 'cest'])
								->value(__tributacaoProdutoLocal($item, 'cest', $l->id))
								!!}
							</div>
							<div class="col-md-2">
								{!!Form::tel('perc_icms[]', '% ICMS')
								->attrs(['class' => 'percentual'])
								->required(__isPlanoFiscal())
								->value(__tributacaoProdutoLocal($item, 'perc_icms', $l->id))
								!!}
							</div>

							<div class="col-md-2">
								{!!Form::tel('perc_pis[]', '% PIS')
								->required(__isPlanoFiscal())
								->attrs(['class' => 'percentual'])
								->value(__tributacaoProdutoLocal($item, 'perc_pis', $l->id))
								!!}
							</div>
							<div class="col-md-2">
								{!!Form::tel('perc_cofins[]', '% COFINS')
								->required(__isPlanoFiscal())
								->attrs(['class' => 'percentual'])
								->value(__tributacaoProdutoLocal($item, 'perc_cofins', $l->id))
								!!}
							</div>
							<div class="col-md-2">
								{!!Form::tel('perc_ipi[]', '% IPI')
								->required(__isPlanoFiscal())
								->attrs(['class' => 'percentual'])
								->value(__tributacaoProdutoLocal($item, 'perc_ipi', $l->id))
								!!}
							</div>
							<div class="col-md-2">
								{!!Form::tel('perc_red_bc[]', '% Red BC')
								->attrs(['class' => 'percentual'])
								->value(__tributacaoProdutoLocal($item, 'perc_red_bc', $l->id))
								!!}
							</div>

							<div class="col-md-2">
								{!!Form::select('origem[]', 'Origem', App\Models\Produto::origens())
								->required(__isPlanoFiscal())
								->attrs(['class' => 'form-select'])
								->value(__tributacaoProdutoLocal($item, 'origem', $l->id))
								!!}
							</div>

							<div class="col-md-6">
								{!!Form::select('cst_csosn[]', 'CST/CSOSN', $listaCTSCSOSN)
								->required(__isPlanoFiscal())
								->attrs(['class' => 'form-select'])
								->value(__tributacaoProdutoLocal($item, 'cst_csosn', $l->id))
								!!}
							</div>
							<div class="col-md-4">
								{!!Form::select('cst_pis[]', 'CST PIS', App\Models\Produto::listaCST_PIS_COFINS())
								->required(__isPlanoFiscal())
								->attrs(['class' => 'form-select'])
								->value(__tributacaoProdutoLocal($item, 'cst_pis', $l->id))
								!!}
							</div>
							<div class="col-md-4">
								{!!Form::select('cst_cofins[]', 'CST COFINS', App\Models\Produto::listaCST_PIS_COFINS())
								->required(__isPlanoFiscal())
								->attrs(['class' => 'form-select'])
								->value(__tributacaoProdutoLocal($item, 'cst_cofins', $l->id))
								!!}
							</div>
							<div class="col-md-4">
								{!!Form::select('cst_ipi[]', 'CST IPI', App\Models\Produto::listaCST_IPI())
								->required(__isPlanoFiscal())
								->attrs(['class' => 'form-select'])
								->value(__tributacaoProdutoLocal($item, 'cst_ipi', $l->id))
								!!}
							</div>

							<div class="col-md-2">
								{!!Form::tel('cfop_estadual[]', 'CFOP Estadual')
								->required(__isPlanoFiscal())
								->attrs(['class' => 'cfop'])
								->value(__tributacaoProdutoLocal($item, 'cfop_estadual', $l->id))
								!!}
							</div>
							<div class="col-md-2">
								{!!Form::tel('cfop_outro_estado[]', 'CFOP Inter Estadual')
								->required(__isPlanoFiscal())
								->attrs(['class' => 'cfop'])
								->value(__tributacaoProdutoLocal($item, 'cfop_outro_estado', $l->id))
								!!}
							</div>

						</div>
					</div>
				</div>
				@endforeach


			</div>
			<div class="col-12" style="text-align: right;">
				<button type="submit" class="btn btn-success btn-action px-5 m-2">Salvar</button>
			</div>

		</div>
		{!!Form::close()!!}
	</div>
</div>

@endsection
@section('js')
<script src="/assets/vendor/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<script src="/assets/js/pages/demo.form-wizard.js"></script>

<script type="text/javascript">

</script>
@endsection

