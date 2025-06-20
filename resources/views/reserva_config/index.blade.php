@extends('layouts.app', ['title' => 'Configuração de Reserva'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração de Reserva</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('config-reserva.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('reserva_config._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
