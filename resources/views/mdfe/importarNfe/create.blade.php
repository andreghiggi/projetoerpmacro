@extends('layouts.app', ['title' => 'Emissão de MDFe com importação de Nfe'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h5 class="mb-0 text-primary">Emissão MDFe com importação de NFe</h5>
        <div style="text-align: right; margin-top: -15px;">
            <a href="{{ route('mdfe.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('mdfe.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('mdfe.importarNfe._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
