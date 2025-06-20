@extends('layouts.app', ['title' => 'Editar Configuração'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Configuração</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('config-fiscal-usuario.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('config-fiscal-usuario.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('configuracao_usuario_emissao._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection

