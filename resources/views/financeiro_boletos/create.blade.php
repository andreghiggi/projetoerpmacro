@extends('layouts.app', ['title' => 'Novo Boleto'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Boleto</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('financeiro-boleto.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('financeiro-boleto.store')
        ->id('form-financeiro')
        !!}
        <div class="pl-lg-4">
            @include('financeiro_boletos._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/financeiro_boleto.js"></script>
@endsection


