@extends('layouts.app', ['title' => 'Novo controle de acesso'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Novo controle de acesso</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('controle-acesso.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('controle-acesso.store')
        !!}
        <div class="pl-lg-4">
            @include('controle_acesso._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')

@endsection
