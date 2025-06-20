@extends('layouts.app', ['title' => 'Configuração Mercado Livre'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração Mercado Livre</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('mercado-livre-config.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('mercado_livre_config._forms')
        </div>
        {!!Form::close()!!}

        @if($item != null)
        <a href="{{ route('mercado-livre.get-code') }}">Solicitar novo token</a>
        @endif

    </div>
</div>
@endsection
