@extends('layouts.app', ['title' => 'Alterar Estado Fiscal'])
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
        @if($tipo == 'devolucao')
        <h4>Alterar Devolução</h4>
        @else
        <h4>Alterar Estado Fiscal NFe</h4>
        @endif
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('nfe.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="row">
        {!!Form::open()
        ->put()
        ->route('nfe.storeEstado', [$item->id])
        ->multipart()
        !!}
        <hr>
        @if($item->cliente)
        <div class="m-3">
            <h5>Cliente: <strong class="text-primary"> {{ $item->cliente->razao_social }}</strong></h5>
            <h6>CNPJ: <strong class="text-success">{{ $item->cliente->cpf_cnpj }}</strong></h6>
            <h6>Data: <strong class="text-success"> {{ __data_pt($item->data_registro, 0) }}</strong></h6>
            <h6>Valor Total: <strong class="text-success"> {{ __moeda($item->total) }}</strong></h6>
            <h6>Cidade: <strong class="text-success"> {{ $item->cliente->cidade->nome }} ({{ $item->cliente->cidade->uf }})</strong></h6>
            <h6>Chave NFe: <strong class="text-success"> {{ $item->chave != "" ? $item->chave : '--' }}</strong></h6>
        </div>
        @else
        <div class="m-3">
            <h5>Fornecedor: <strong class="text-primary"> {{ $item->fornecedor->razao_social }}</strong></h5>
            <h6>CNPJ: <strong class="text-success">{{ $item->fornecedor->cpf_cnpj }}</strong></h6>
            <h6>Data: <strong class="text-success"> {{ __data_pt($item->data_registro, 0) }}</strong></h6>
            <h6>Valor Total: <strong class="text-success"> {{ __moeda($item->total) }}</strong></h6>
            <h6>Cidade: <strong class="text-success"> {{ $item->fornecedor->cidade->info }}</strong></h6>
            <h6>Chave NFe: <strong class="text-success"> {{ $item->chave != "" ? $item->chave : '--' }}</strong></h6>
        </div>
        @endif
        <hr>
        <div class="row m-3">
            <div class="col-md-3">
                {!!Form::select('estado_emissao', 'Estado',
                ['novo' => 'Novo', 'rejeitado' => 'Rejeitado', 'cancelado' => 'Cancelado', 'aprovado' => 'Aprovado'])
                ->attrs(['class' => 'form-select'])->value(isset($item) ? $item->estado : '')!!}
            </div>
            <div class="col-md-6">
                <div class="col-md-5 file-certificado">
                    {!! Form::file('file', 'Arquivo XML')
                    ->attrs(['accept' => '.xml']) !!}
                    <span class="text-danger" id="filename"></span>
                </div>
            </div>
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary px-5">Salvar</button>
            </div>
        </div>
        <input type="hidden" name="tipo" value="{{ $tipo }}">
        {!!Form::close()!!}
    </div>
</div>
@endsection
