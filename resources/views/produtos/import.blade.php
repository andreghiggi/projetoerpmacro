@extends('layouts.app', ['title' => 'Importar Produtos'])
@section('css')
<style type="text/css">
    .btn-file {
        position: relative;
        overflow: hidden;
    }

    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Importar Produtos</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <p>Campos com <span class="text-danger">*</span> são obrigatórios</p>
        <div class="row">
            <div class="col-12 col-md-6">
                <h5><strong class="text-primary">Nome</strong><span class="text-danger">*</span> - tipo texto</h5>
                <h5><strong class="text-primary">Categoria</strong> - tipo texto</h5>
                <h5><strong class="text-primary">Valor de venda</strong><span class="text-danger">*</span> - tipo moeda</h5>
                <h5><strong class="text-primary">Valor de compra</strong> - tipo moeda</h5>
                <h5><strong class="text-primary">NCM</strong><span class="text-danger">*</span> - tipo númerico</h5>
                <h5><strong class="text-primary">Código de barras</strong> - tipo texto</h5>
                <h5><strong class="text-primary">CEST</strong> - tipo númerico</h5>
                <h5><strong class="text-primary">CST/CSOSN</strong><span class="text-danger">*</span> - tipo númerico</h5>
                <h5><strong class="text-primary">CST PIS</strong><span class="text-danger">*</span> - tipo númerico</h5>
                <h5><strong class="text-primary">CST COFINS</strong><span class="text-danger">*</span> - tipo númerico</h5>
                <h5><strong class="text-primary">CST IPI</strong><span class="text-danger">*</span> - tipo númerico</h5>
                <h5><strong class="text-primary">% Red. base de cálculo</strong> - tipo percentual</h5>
                <h5><strong class="text-primary">Origem</strong> - tipo númerico</h5>
                <h5><strong class="text-primary">Código de enquadramento IPI</strong> - tipo númerico</h5>
                <h5><strong class="text-primary">Referência</strong> - tipo texto</h5>
            </div>
            <div class="col-12 col-md-6">
                <h5><strong class="text-primary">CFOP estadual</strong><span class="text-danger">*</span> - tipo númerico</h5>
                <h5><strong class="text-primary">CFOP outro estado</strong><span class="text-danger">*</span> - tipo númerico</h5>
                <h5><strong class="text-primary">Código do benefício</strong> - tipo texto</h5>
                <h5><strong class="text-primary">Unidade</strong> - tipo texto</h5>
                <h5><strong class="text-primary">Origem</strong> - tipo númerico</h5>
                <h5><strong class="text-primary">Gerenciar Estoque</strong> - tipo binário 1 ou 0</h5>
                <h5><strong class="text-primary">%ICMS</strong> - tipo percentual</h5>
                <h5><strong class="text-primary">%PIS</strong> - tipo percentual</h5>
                <h5><strong class="text-primary">%COFINS</strong> - tipo percentual</h5>
                <h5><strong class="text-primary">%IPI</strong> - tipo percentual</h5>
                <h5><strong class="text-primary">CFOP entrada estadual</strong> - tipo númerico</h5>
                <h5><strong class="text-primary">CFOP entrada outro estado</strong> - tipo númerico</h5>
                <h5><strong class="text-primary">Estoque</strong> - tipo númerico</h5>
                <h5><strong class="text-primary">Estoque mínimo</strong> - tipo númerico</h5>
            </div>
        </div>
        <div class="col-12 col-md-2">
            <a href="{{ route('produtos.import-download') }}" class="btn btn-primary">
                <i class="ri-file-download-line"></i>
                Download Modelo
            </a>
        </div>
    </div>
    <div class="card-footer">
        <hr>
        <form id="form-import" class="row" method="post" action="{{ route('produtos.import-store') }}" enctype="multipart/form-data">
            @csrf
            <p>Importar modelo preenchido</p>
            <div class="form-group validated col-12 col-md-2">
                <label class="col-form-label">.xls/.xlsx</label>
                <div class="">
                    <span class="btn btn-success btn-file">
                        <i class="ri-file-search-line"></i>
                        Procurar arquivo<input accept=".xls, .xlsx" name="file" type="file" id="file">
                    </span>
                </div>
                <span class="text-danger" id="filename"></span>
            </div>

            @if(__countLocalAtivo() > 1)
            <div class="col-md-3" style="margin-top: 15px;">
                <label for="">Disponibilidade</label>

                <select required class="select2 form-control select2-multiple" data-toggle="select2" name="locais[]" multiple="multiple">
                    @foreach(__getLocaisAtivoUsuario() as $local)
                    <option @if(in_array($local->id, (isset($item) ? $item->locais->pluck('localizacao_id')->toArray() : []))) selected @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
                    @endforeach
                </select>
            </div>
            @else

            <input type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
            @endif

            <div class="col-md-2">
                <button type="button" class="btn btn-dark btn-save" style="margin-top: 36px;">
                    <i class="ri-uninstall-fill"></i>
                    Importar produtos
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript">
    $('#file').change(function() {
        
    });

    $('.btn-save').click(() => {
        $('#form-import').submit();
        $body = $("body");
        $body.addClass("loading");
    })
</script>
@endsection
