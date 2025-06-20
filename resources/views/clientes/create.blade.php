@extends('layouts.app', ['title' => 'Novo Cliente'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Cliente</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('clientes.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('clientes.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('clientes._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')

@endsection
