@extends('layouts.app', ['title' => 'Receber Contas'])
@section('content')
<div class="page-content">
	<div class="card border-top border-0 border-4 border-primary">
		<div class="card-body p-5">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
				<div class="ms-auto">
					<a href="{{ route('conta-receber.index')}}" type="button" class="btn btn-danger btn-sm">
						<i class="ri-arrow-left-double-fill"></i>Voltar
					</a>
				</div>
			</div>
			<div class="card-title d-flex align-items-center">
				<h4 class="mb-0 text-primary">Receber contas</h4>
			</div>
			<hr>
			
			{!!Form::open()
			->put()
			->route('conta-receber.receive-select')
			!!}
			<div class="pl-lg-4">
				@foreach($data as $key => $item)
				<div class="row">
					<div class="col-md-6">
						<h5>Data de cadastro: <strong class="">{{ __data_pt($item->created_at) }}</strong></h5>
						<h5>Valor: <strong class="">R$ {{ __moeda($item->valor_integral) }}</strong></h5>
						@if($item->categoria)
						<h5>Categoria: <strong class="">{{ $item->categoria->nome }}</strong></h5>
						@endif
					</div>
					<div class="col-md-6">
						<h5>Data de vencimento: <strong class="">{{ __data_pt($item->data_vencimento, false) }}</strong></h5>
						<h5>Referência: <strong class="">{{ $item->referencia ? $item->referencia : '--' }}</strong></h5>
					</div>
				</div>
				@include('conta-receber._forms_pay_select')

				@endforeach

				<div class="col-12">
					<button type="submit" class="btn btn-success px-5">Receber</button>
				</div>
			</div>
			{!!Form::close()!!}
		</div>
	</div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/controla_conta_empresa.js"></script>
<script type="text/javascript">
	setTimeout(() => {
		$(".conta_empresa").each(function (e, v) {
			$(this).select2({
				minimumInputLength: 2,
				language: "pt-BR",
				placeholder: "Digite para buscar a conta",
				width: "100%",
				ajax: {
					cache: true,
					url: path_url + "api/contas-empresa",
					dataType: "json",
					data: function (params) {
						console.clear();
						let empresa_id = $('#empresa_id').val()
						var query = {
							pesquisa: params.term,
							empresa_id: empresa_id
						};
						return query;
					},
					processResults: function (response) {
						var results = [];

						$.each(response, function (i, v) {
							var o = {};
							o.id = v.id;

							o.text = v.nome;
							o.value = v.id;
							results.push(o);
						});
						return {
							results: results,
						};
					},
				},
			});
		});
	}, 100);
</script>
@endsection