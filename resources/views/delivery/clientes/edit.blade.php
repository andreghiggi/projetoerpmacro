@extends('layouts.app', ['title' => 'Editar Cliente'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Cliente</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('clientes-delivery.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('clientes-delivery.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('delivery.clientes._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
