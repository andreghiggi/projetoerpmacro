@extends('layouts.app', ['title' => 'Novo Tratamento'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Tratamento</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('tratamentos-otica.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('tratamentos-otica.store')
        !!}
        <div class="pl-lg-4">
            @include('tratamento_otica._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
