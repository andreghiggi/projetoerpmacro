@extends('layouts.app', ['title' => 'Novo Tipo de Armação'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Tipo de Armação</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('tipo-armacao.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('tipo-armacao.store')
        !!}
        <div class="pl-lg-4">
            @include('tipo_armacao._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
