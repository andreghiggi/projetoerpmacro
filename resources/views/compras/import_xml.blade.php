@extends('layouts.app', ['title' => 'Importação de XML'])
@section('css')
<style type="text/css">
    .ri-information-line:hover{
        cursor: pointer;
    }
</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-header">

        <h4>Importação de XML</h4>
        @isset($dadosXml)
        <h5>Chave <strong class="text-success">{{ $dadosXml['chave'] }}</strong></h5>
        @endif
        <div style="text-align: right; margin-top: -35px;">
            @if(__countLocalAtivo() > 1 && isset($caixa))
            <h5 class="mt-2">Local: <strong class="text-danger">{{ $caixa->localizacao ? $caixa->localizacao->descricao : '' }}</strong></h5>
            @endif
            <a href="{{ route('compras.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('compras.finish-xml')
        ->multipart()
        !!}

        <div class="pl-lg-4">
            @include('compras._forms_xml')
        </div>
        {!!Form::close()!!}
    </div>
</div>

@include('modals._altera_produto_xml')
@include('modals._modal_show_xml')
@section('js')
<script src="/js/nfe.js"></script>
<script src="/js/import_xml.js"></script>
@endsection
@endsection
