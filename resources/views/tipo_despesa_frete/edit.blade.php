@extends('layouts.app', ['title' => 'Tipos de Despesa de Frete'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Tipo de Despesa de Frete</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('tipo-despesa-frete.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('tipo-despesa-frete.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('tipo_despesa_frete._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection


