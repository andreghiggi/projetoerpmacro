@extends('layouts.app', ['title' => 'Nova Proposta'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Proposta</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('planejamento-custo.show', [$item->id]) }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('planejamento-custo.proposta-store', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('planejamento_custo._forms_proposta')
        </div>
        {!!Form::close()!!}
        @include('modals._novo_cliente')

    </div>
</div>


@endsection
@section('js')
<script type="text/javascript" src="/js/planejamento_proposta.js"></script>
@endsection
