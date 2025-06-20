@extends('layouts.app', ['title' => 'Unidade de Medida'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Unidade de Medida</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('unidades-medida.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('unidades-medida.store')
        !!}
        <div class="pl-lg-4">
            @include('unidades_medida._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection


