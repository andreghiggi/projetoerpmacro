@extends('layouts.app', ['title' => 'Nova Localização'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Localização</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('localizacao.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('localizacao.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('localizacao._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

