@extends('layouts.app', ['title' => 'Transferência de estoque'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Transferência de estoque</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('transferencia-estoque.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('transferencia-estoque.store')
        !!}
        <div class="pl-lg-4">
            @include('transferencia_estoque._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/transferencia_estoque.js"></script>
@endsection
