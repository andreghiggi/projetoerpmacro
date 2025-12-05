@extends('layouts.app', ['title' => 'Novo Projeto'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Projeto</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('projeto-custo.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('projeto-custo.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('projeto_custo._forms')
        </div>
        {!!Form::close()!!}
        @include('modals._novo_cliente')

    </div>
</div>

@include('modals._dimensao_item_planejamento')
@endsection
@section('js')

@endsection
