@extends('layouts.app', ['title' => 'Editar Conta Pagar'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h3>Editar Conta Pagar</h3>
        <div style="text-align: right;" class="">
            <a href="{{ route('conta-pagar.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('conta-pagar.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('conta-pagar._forms', ['edit' => true])
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
