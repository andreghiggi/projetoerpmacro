@extends('layouts.app', ['title' => 'Relatório Ordem de Serviço'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Relatório Ordem Serviço</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('ordem-servico.show', $ordem->id) }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('ordem-servico.store-relatorio', [$ordem->id])
        !!}
        <div class="pl-lg-4">
            @include('ordem_servico._forms_relatorio')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
