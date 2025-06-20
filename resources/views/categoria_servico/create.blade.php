@extends('layouts.app', ['title' => 'Categoria Serviço'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Categoria de Serviço</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('categoria-servico.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('categoria-servico.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('categoria_servico._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')

@endsection
