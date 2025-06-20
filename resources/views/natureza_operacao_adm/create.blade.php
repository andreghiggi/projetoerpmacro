@extends('layouts.app', ['title' => 'Nova Natureza'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Natureza</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('natureza-operacao-adm.index', ['empresa='. $empresa->id]) }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <h5>Empresa <strong class="text-primary">{{ $empresa->nome }}</strong></h5>
        
        {!!Form::open()
        ->post()
        ->route('natureza-operacao-adm.store', ['empresa='. $empresa->id])
        !!}
        <div class="pl-lg-4">
            @include('natureza_operacao._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
