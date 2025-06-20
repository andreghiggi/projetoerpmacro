@extends('layouts.app', ['title' => 'Editar Variação'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Variação</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('variacoes.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('variacoes.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('variacao_modelo._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
