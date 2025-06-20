@extends('layouts.app', ['title' => 'Novo Cupom'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Cupom</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('cupom-desconto.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('cupom-desconto.store')
        !!}
        <div class="pl-lg-4">
            @include('cupom_desconto._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
