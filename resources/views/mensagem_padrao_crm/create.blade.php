@extends('layouts.app', ['title' => 'Nova Mensagem Padrão'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Mensagem Padrão</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('mensagem-padrao-crm.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('mensagem-padrao-crm.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('mensagem_padrao_crm._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

