@extends('layouts.app', ['title' => 'Editar Natureza'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Natureza</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('natureza-operacao-adm.index', ['empresa='. $empresa->id]) }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <h5>Empresa <strong class="text-primary">{{ $empresa->nome }}</strong></h5>
        
        {!!Form::open()->fill($item)
        ->put()
        ->route('natureza-operacao-adm.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('natureza_operacao._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
