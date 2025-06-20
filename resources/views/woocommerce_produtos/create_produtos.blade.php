@extends('layouts.app', ['title' => 'Produtos Woocommerce'])
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
        <h4>Cadastrando produtos do Woocommerce</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('woocommerce-produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('woocommerce-produtos.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('woocommerce_produtos._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>

@section('js')
<script src="/js/woocommerce_produtos.js"></script>
@endsection
@endsection
