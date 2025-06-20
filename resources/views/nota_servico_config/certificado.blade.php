@extends('layouts.app', ['title' => 'Upload de certificado'])

@section('css')
<style type="text/css">
    input[type="file"] {
        display: none;
    }

    .file-certificado label {
        padding: 8px 8px;
        width: 100%;
        background-color: #8833FF;
        color: #FFF;
        text-transform: uppercase;
        text-align: center;
        display: block;
        margin-top: 20px;
        cursor: pointer;
        border-radius: 5px;
    }

    .card-body strong {
        color: #8833FF;
    }

</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-header">

        <h4>Configuração NFSe

            @if($certificadoApi->codigo != 200)
            <h5 class="text-danger">{{ $certificadoApi->mensagem }}</h5>
            @else
            <h5 class="text-success">Certificado OK <i class="la la-check text-success"></i></h5>
            @endif
        </h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('nota-servico-config.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>

    </div>
    <div class="card-body">


        {!!Form::open()
        ->post()
        ->route('nota-servico-config.upload-certificado')
        ->multipart()
        !!}
        
        <div class="row m-2">
            <div class="col-md-5 file-certificado">
                {!! Form::file('file', 'Certificado Digital')->value(isset($item) ? false : true) !!}
                <span class="text-danger" id="filename"></span>
            </div>
            <div class="col-md-2">
                {!! Form::tel('senha', 'Senha do certificado')->required() !!}
            </div>
        </div>

        <hr class="mt-4">
        <div class="col-12" style="text-align: right;">
            <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection