@extends('layouts.app', ['title' => 'Cadastrar Categoria Nuvem Shop'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Cadastrar Categoria Nuvem Shop</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('nuvem-shop-categorias.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('nuvem-shop-categorias.store')
        !!}
        <div class="pl-lg-4">
            @include('nuvem_shop_categorias._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
