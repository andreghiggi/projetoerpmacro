@extends('layouts.app', ['title' => 'Configuração da Empresa'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração da Empresa</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('empresas.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($empresa)
        ->post()
        ->route('config.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">

            @isset($empresa->id)
            @include('config.edit')
            @else
            @include('empresas._forms')
            @endisset
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
