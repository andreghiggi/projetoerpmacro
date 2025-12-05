@extends('layouts.app', ['title' => 'Receber Conta'])
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
				<h4 class="mb-0 text-primary">Receber conta</h4>
			</div>
			<hr>
			
			{!!Form::open()
			->put()
			->route('conta-receber.pay-put', [$item->id])
			!!}
			<div class="pl-lg-4">
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
				@include('conta-receber._forms_pay')
			</div>
			{!!Form::close()!!}
		</div>
	</div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/controla_conta_empresa.js"></script>
@endsection