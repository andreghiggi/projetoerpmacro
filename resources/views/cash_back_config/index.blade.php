@extends('layouts.app', ['title' => 'Configuração de CashBack'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração de CashBack</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('cash-back-config.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('cash-back-config.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('cash_back_config._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
