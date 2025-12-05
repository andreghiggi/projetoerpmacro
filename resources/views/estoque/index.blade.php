@extends('layouts.app', ['title' => 'Estoque'])
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
                    @can('estoque_create')
                    <div class="col-md-2 col-12 mt-1">
                        <a href="{{ route('estoque.create') }}" class="btn btn-success">
                            <i class="ri-add-circle-fill"></i>
                            Adicionar estoque
                        </a>
                    </div>
                    <div class="col-md-10 col-12 mt-1"  style="text-align: right;">
                        <a href="{{ route('estoque.retirada') }}" class="btn btn-light">
                            <i class="ri-inbox-archive-fill"></i>
                            Retirada de Estoque
                        </a>
                        <a href="{{ route('apontamento.create') }}" class="btn btn-info">
                            <i class="ri-edit-box-fill"></i>
                            Apontamento de Produção
                        </a>
                    </div>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::text('produto', 'Pesquisar por produto')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('categoria_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        @if(__countLocalAtivo() > 1)
                        <div class="col-md-2">
                            {!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        @endif
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('estoque.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">

                    @if($tipoExibe == 'tabela')
                    @include('estoque.partials.tabela')
                    @else
                    @include('estoque.partials.card')
                    @endif
                </div>
                <br>
                {!! $data->appends(request()->all())->links() !!}

            </div>
        </div>
    </div>
</div>
@endsection
