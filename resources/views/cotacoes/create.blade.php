@extends('layouts.app', ['title' => 'Nova Cotação'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Cotação</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('cotacoes.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('cotacoes.store')
        ->id('form-cotacao')
        !!}
        <div class="pl-lg-4">
            @include('cotacoes._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection


