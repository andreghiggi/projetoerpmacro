@extends('layouts.app', ['title' => 'Nova Categoria'])

@section('content')

<div class="mt-3">
    <div class="row">
        {!!Form::open()
        ->post()
        ->route('ifood-categoria-produtos.store')
        !!}
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h4>Nova Categoria</h4>
                    <div style="text-align: right; margin-top: -35px;">
                        <a href="{{ route('ifood-categoria-produtos.index') }}" class="btn btn-danger btn-sm px-3">
                            <i class="ri-arrow-left-double-fill"></i>Voltar
                        </a>
                    </div>
                    <hr>
                    @include('ifood_categorias._forms')
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>

@endsection
