@extends('layouts.app', ['title' => 'Editar produto Nuvem Shop'])
@section('css')
<style type="text/css">
    input:read-only {
        background-color: #CCCCCC;
    }
</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Produto Nuvem Shop</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('nuvem-shop-produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->put()
        ->route('nuvem-shop-produtos.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('nuvem_shop_produtos._forms_edit')
        </div>
        {!!Form::close()!!}
    </div>
</div>

@endsection
