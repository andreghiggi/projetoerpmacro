@extends('layouts.app', ['title' => 'Relação Dados Fornecedor'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Relação Dados Fornecedor</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('relacao-dados-fornecedor.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('relacao-dados-fornecedor.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('relacao_dados_fornecedor._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
