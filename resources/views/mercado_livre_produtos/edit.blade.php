@extends('layouts.app', ['title' => 'Editar produto ML'])
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
        <h4>Editar produto do Mercado Livre</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('mercado-livre-produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->put()
        ->route('mercado-livre-produtos.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('mercado_livre_produtos._forms_edit')
        </div>
        {!!Form::close()!!}
    </div>
</div>

@section('js')
<script src="/js/mercado_livre_produtos.js"></script>
@endsection
@endsection
