@extends('layouts.app', ['title' => 'Nova Empresa'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Empresa</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('empresas.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('empresas.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('empresas._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection


