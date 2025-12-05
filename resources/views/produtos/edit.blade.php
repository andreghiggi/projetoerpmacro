@extends('layouts.app', ['title' => 'Editar Produto'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Produto</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ url()->previous() }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->id('form-produto')
        ->route('produtos.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('produtos._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@include('modals._marca')
@include('modals._categoria_produto')
@endsection
