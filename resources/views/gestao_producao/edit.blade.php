@extends('layouts.app', ['title' => 'Editar Gestão de Produção'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Gestão de Produção</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('gestao-producao.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('gestao-producao.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('gestao_producao._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>

@endsection
@section('js')
<script type="text/javascript" src="/js/gestao_producao.js"></script>
@endsection
