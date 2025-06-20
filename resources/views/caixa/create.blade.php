@extends('layouts.app', ['title' => 'Abertura de caixa'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Abrir Caixa</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('caixa.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('caixa.store')
        !!}
        <div class="pl-lg-4">
            @include('caixa._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/controla_conta_empresa.js"></script>
@endsection
