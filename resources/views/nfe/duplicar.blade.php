@extends('layouts.app', ['title' => 'Nova Venda'])
@section('content')

<div class="card mt-1">
    <div class="card-header">

        <h4>Nova Venda</h4>
        <div style="text-align: right; margin-top: -35px;">
            @if(__countLocalAtivo() > 1 && isset($caixa))
            <h5 class="mt-2">Local: <strong class="text-danger">{{ $caixa->localizacao ? $caixa->localizacao->descricao : '' }}</strong></h5>
            @endif
            <a href="{{ route('nfe.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
        <input type="hidden" id="is_orcamento" value="0">
        
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('nfe.store', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('nfe._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@section('js')
<script src="/js/nfe.js"></script>
@endsection
@endsection
