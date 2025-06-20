@extends('layouts.app', ['title' => 'Nova Conta'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Conta</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('contas-boleto.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('contas-boleto.store')
        !!}
        <div class="pl-lg-4">
            @include('contas_boleto._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
