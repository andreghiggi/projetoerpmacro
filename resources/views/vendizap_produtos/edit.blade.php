@extends('layouts.app', ['title' => 'Editar Produto VendiZap'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Produto VendiZap</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('vendizap-produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <a href="{{ route('produtos.edit', [$item->id]) }}">Editar cadastro principal</a>
        {!!Form::open()->fill($item)
        ->put()
        ->route('vendizap-produtos.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('vendizap_produtos._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
