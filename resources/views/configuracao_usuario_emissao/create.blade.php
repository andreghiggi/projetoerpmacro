@extends('layouts.app', ['title' => 'Nova Configuração'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Configuração</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('config-fiscal-usuario.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('config-fiscal-usuario.store')
        !!}
        <div class="pl-lg-4">
            @include('configuracao_usuario_emissao._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection

