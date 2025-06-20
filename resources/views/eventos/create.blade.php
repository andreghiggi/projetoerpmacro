@extends('layouts.app', ['title' => 'Novo Evento'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Evento</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('evento-funcionarios.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('evento-funcionarios.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('eventos._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection