@extends('layouts.app', ['title' => 'XML para Devolução'])
@section('content')

<div class="card mt-1">
    <div class="card-header">

        <h4>XML para Devolução</h4>
        <div style="text-align: right; margin-top: -35px;">
            @if(__countLocalAtivo() > 1 && isset($caixa))
            <h5 class="mt-2">Local: <strong class="text-danger">{{ $caixa->localizacao ? $caixa->localizacao->descricao : '' }}</strong></h5>
            @endif
            <a href="{{ route('devolucao.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('devolucao.finish-xml')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('devolucao._forms_xml')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@section('js')
<script src="/js/devolucao.js"></script>
@endsection
@endsection
