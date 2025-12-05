@extends('layouts.app', ['title' => 'Categoria Conta'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Categoria de Conta</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('categoria-conta.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('categoria-conta.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('categoria_conta._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection


