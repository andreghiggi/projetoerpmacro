@extends('layouts.app', ['title' => 'Nova Conta Receber'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Conta Receber <strong class="text-muted">Frete #{{ $item->numero_sequencial }}</strong></h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('fretes.show', [$item->id]) }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('conta-receber.store')
        ->multipart()
        !!}

        <input type="hidden" name="redirect" value="{{ route('fretes.show', [$item->id]) }}">
        <input type="hidden" name="frete_id" value="{{ $item->id }}">
        <div class="pl-lg-4">
            @include('conta-receber._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>

@include('modals._novo_cliente')

@endsection
