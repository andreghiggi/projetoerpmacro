@extends('layouts.app', ['title' => 'Editar Evento'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Evento</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('evento-funcionarios.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('evento-funcionarios.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('eventos._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection