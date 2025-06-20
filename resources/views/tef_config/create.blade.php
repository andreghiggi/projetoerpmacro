@extends('layouts.app', ['title' => 'Nova Configuração de TEF'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Configuração de TEF</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('tef-config.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('tef-config.store')
        !!}
        <div class="pl-lg-4">
            @include('tef_config._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
