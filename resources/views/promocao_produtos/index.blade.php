@extends('layouts.app', ['title' => 'Promoção'])

@section('css')
<style type="text/css">
    .img-wrapper {
        height: 180px;
        overflow: hidden;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        background-color: #f8f9fa;
    }
    .produto-img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .produto-card {
        border-radius: 1rem;
        transition: all 0.3s ease;
        background-color: #fff;
    }
    .produto-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }
    .produto-card:hover .produto-img {
        transform: scale(1.05);
    }
</style>
@endsection

@section('content')
<div class="mt-1">
	<div class="row">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-3">
						@can('promocao_produtos_create')
						<a href="{{ route('promocao-produtos.create') }}" class="btn btn-success">
							<i class="ri-add-circle-fill"></i>
							Nova Promoção
						</a>
						@endcan
					</div>

					<div class="col-md-6"></div>
					<div class="col-md-3 text-end">
						@can('promocao_produtos_create')
						<a href="{{ route('promocao-produtos.group') }}" class="btn btn-primary">
							<i class="ri-list-ordered"></i>
							Promoção em Grupo
						</a>
						@endcan
					</div>

				</div>
				<hr class="mt-3">
				<div class="col-lg-12">
					{!!Form::open()->fill(request()->all())
					->get()
					!!}
					<div class="row mt-3 g-1">
						<div class="col-md-3">
							{!!Form::select('produto_id', 'Produto')
							->options(isset($produto) ? [$produto->id => $produto->nome] : [])
							!!}
						</div>

						<div class="col-md-2">
							{!!Form::date('start_date', 'Data inicial')
							!!}
						</div>
						<div class="col-md-2">
							{!!Form::date('end_date', 'Data final')
							!!}
						</div>
						<div class="col-md-2">
							{!!Form::select('status', 'Status', ['' => 'Todos', 1 => 'Ativo', 0 => 'Pendente'])
							->attrs(['class' => 'form-select'])
							!!}
						</div>
						<div class="col-md-2">
							{!!Form::select('ativa', 'Ativa', ['' => 'Todos', 1 => 'Sim', -1 => 'Não'])
							->attrs(['class' => 'form-select'])
							!!}
						</div>
						<div class="col-md-4 text-left ">
							<br>
							<button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
							<a id="clear-filter" class="btn btn-danger" href="{{ route('promocao-produtos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
						</div>
					</div>
					{!!Form::close()!!}
				</div>
				<div class="col-md-12 mt-3">
					<p>Total de registros: <strong>{{ $data->total() }}</strong></p>

					@if($tipoExibe == 'tabela')
                    @include('promocao_produtos.partials.tabela')
                    @else
                    @include('promocao_produtos.partials.card')
                    @endif
					
				</div>
				{!! $data->appends(request()->all())->links() !!}
			</div>
		</div>
	</div>
</div>

@endsection