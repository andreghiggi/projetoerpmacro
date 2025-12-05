@extends('layouts.app', ['title' => 'Nova Impressora'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Impressora</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('impressoras-pedido.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('impressoras-pedido.store')
        !!}
        <div class="pl-lg-4">
            @include('impressoras_pedido._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
