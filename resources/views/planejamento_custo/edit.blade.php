@extends('layouts.app', ['title' => 'Editar Planejamento'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Planejamento</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('planejamento-custo.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('planejamento-custo.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('planejamento_custo._forms')
        </div>
        {!!Form::close()!!}
        @include('modals._novo_cliente')

    </div>
</div>

@include('modals._dimensao_item_planejamento')
@endsection
@section('js')
<script type="text/javascript" src="/js/planejamento_custo.js"></script>
@endsection
