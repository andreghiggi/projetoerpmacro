@extends('layouts.app', ['title' => 'Alterar Estado Fiscal MDFe'])
@section('content')

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


<div class="card mt-3">
    <div class="card-header">
        <h4>Alterar Estado Fiscal MDFe</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('mdfe.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="row">
        {!!Form::open()
        ->put()
        ->route('mdfe.storeEstado', [$item->id])
        ->multipart()
        !!}
        <hr>
        <div class="m-3">
            <h5>MDFe código: <strong class="text-primary">{{ $item->id }}</strong></h5>
            <h5>Data de cadastro: <strong class="text-primary">{{ __data_pt($item->created_at) }}</strong></h5>
            <h5>Valor de carga: <strong class="text-primary">R$ {{ __moeda($item->valor_carga) }}</strong></h5>
            <h5>Chave: <strong class="text-primary">{{ $item->chave }}</strong></h5>
        </div>
        <hr>
        <div class="row m-3">
            <div class="col-md-3">
                {!!Form::select('estado_emissao', 'Estado',
                ['novo' => 'Novo', 'rejeitado' => 'Rejeitado', 'cancelado' => 'Cancelado', 'aprovado' => 'Aprovado'])
                ->attrs(['class' => 'form-select'])->value(isset($item) ? $item->estado_emissao : '')!!}
            </div>
            <div class="col-md-6">
                <div class="col-md-5 file-certificado">
                    {!! Form::file('file', 'Arquivo XML')
                    ->attrs(['accept' => '.xml']) !!}
                    <span class="text-danger" id="filename"></span>
                </div>
            </div>
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-success px-5">Salvar</button>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
