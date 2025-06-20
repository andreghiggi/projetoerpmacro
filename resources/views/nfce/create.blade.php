@extends('layouts.app', ['title' => 'Nova NFCe'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova NFCe</h4>
        <div style="text-align: right; margin-top: -35px;">
            @if(__countLocalAtivo() > 1 && isset($caixa))
            <h5 class="mt-2">Local: <strong class="text-danger">{{ $caixa->localizacao ? $caixa->localizacao->descricao : '' }}</strong></h5>
            @endif
            <a href="{{ route('nfce.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->id('form-nfce')
        ->route('nfce.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('nfce._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@include('modals._novo_cliente')

@section('js')
<script src="/js/nfce.js"></script>
<script src="/js/novo_cliente.js"></script>

@endsection
@endsection
