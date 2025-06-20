@extends('layouts.app', ['title' => 'Apontar Itens'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Apontar Itens</h4>
        <h5 class="text-danger">#{{ $item->numero_sequencial }} - REF: {{ $item->referencia }}</h5>
        <a class="btn btn-dark" href="{{ route('inventarios.itens', [$item->id]) }}">
            <i class="ri-file-list-2-fill"></i>
            Ver itens apontados
        </a>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('inventarios.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">

        {!!Form::open()
        ->post()
        ->route('inventarios.store-item', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('inventarios._forms_item')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
