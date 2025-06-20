@extends('layouts.app', ['title' => 'Nova Natureza'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Natureza</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('natureza-operacao.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('natureza-operacao.store')
        !!}
        <div class="pl-lg-4">
            @include('natureza_operacao._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
