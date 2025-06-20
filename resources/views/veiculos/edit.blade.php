@extends('layouts.app', ['title' => 'Editar Veículo'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Cadastro de Usuário</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('veiculos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('veiculos.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('veiculos._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
