@extends('layouts.app', ['title' => 'Atribuir Eventos'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Evento</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('funcionario-eventos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('funcionario-eventos.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('funcionario_evento._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection