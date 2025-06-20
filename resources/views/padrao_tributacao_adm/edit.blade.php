@extends('layouts.app', ['title' => 'Editar Padrão'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Padrão</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('produtopadrao-tributacao-adm.index', ['empresa='. $empresa->id]) }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <h5>Empresa <strong class="text-primary">{{ $empresa->nome }}</strong></h5>
        
        {!!Form::open()->fill($item)
        ->put()
        ->route('produtopadrao-tributacao-adm.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('padrao_tributacao._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
