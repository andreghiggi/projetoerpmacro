@extends('layouts.app', ['title' => 'Configuração de Agendamento'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração de Agendamento</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('config-agendamento.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('config_agendamento._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
