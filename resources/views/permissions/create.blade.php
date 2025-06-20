@extends('layouts.app', ['title' => 'Nova Permissão'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Permissão</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('permissions.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('permissions.store')
        !!}
        <div class="pl-lg-4">
            @include('permissions._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')

@endsection
