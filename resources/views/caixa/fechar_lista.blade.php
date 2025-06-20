@extends('layouts.app', ['title' => 'Fechando caixa'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Fechar Caixa</h4>
        @if($item->contaEmpresa)
        <h6 class="text-danger">{{ $item->contaEmpresa->nome }}</h6>
        @endif
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('caixa.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('caixa.fechar-tipos-pagamento', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('caixa._listar_pagamentos')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/controla_conta_empresa.js"></script>
<script type="text/javascript" src="/js/conta_empresa.js"></script>
@endsection
