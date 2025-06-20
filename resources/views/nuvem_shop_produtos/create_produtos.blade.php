@extends('layouts.app', ['title' => 'Produtos Nuvem Shop'])
@section('css')
<style type="text/css">
    input:read-only {
        background-color: #CCCCCC;
    }
</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Cadastrando Produtos do Nuvem Shop</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('nuvem-shop-produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('nuvem-shop-produtos.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('nuvem_shop_produtos._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>

@section('js')
<script src="/js/nuvem_shop_produtos.js"></script>
@endsection
@endsection
