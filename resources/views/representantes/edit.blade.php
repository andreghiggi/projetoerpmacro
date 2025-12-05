@extends('layouts.app', ['title' => 'Editar Representante'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Representante</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('representantes.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('representantes.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('representantes._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection


