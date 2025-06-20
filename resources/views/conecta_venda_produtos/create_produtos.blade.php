@extends('layouts.app', ['title' => 'Produtos Conecta Venda'])
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
            <h4>Cadastrando produtos do Conecta Venda</h4>
            <div style="text-align: right; margin-top: -35px;">
                <a href="{{ route('conecta-venda-produtos.index') }}" class="btn btn-danger btn-sm px-3">
                    <i class="ri-arrow-left-double-fill"></i>Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            {!!Form::open()
            ->post()
            ->route('conecta-venda-produtos.store')
            ->multipart()
            !!}
            <div class="pl-lg-4">
                @include('conecta_venda_produtos._forms')
            </div>
            {!!Form::close()!!}
        </div>
    </div>

    @section('js')
        <script src="/js/mercado_livre_produtos.js"></script>
    @endsection
@endsection
