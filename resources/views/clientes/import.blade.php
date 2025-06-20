@extends('layouts.app', ['title' => 'Importar Clientes'])
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
        <h4>Importar Clientes</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('clientes.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <p>Campos com <span class="text-danger">*</span> são obrigatórios</p>
        <div class="row">
            <div class="col-12 col-md-6">
                <h5><strong class="text-primary">RAZÃO SOCIAL</strong><span class="text-danger">*</span> - tipo texto</h5>
                <h5><strong class="text-primary">NOME FANTASIA</strong> - tipo texto</h5>
                <h5><strong class="text-primary">CPF/CNPJ</strong><span class="text-danger">*</span> - tipo númerico</h5>
                <h5><strong class="text-primary">IE</strong> - tipo númerico</h5>
                <h5><strong class="text-primary">CONTRIBUINTE</strong> - tipo binário 0 ou 1</h5>
                <h5><strong class="text-primary">CONSUMIDOR FINAL</strong> - tipo binário 0 ou 1</h5>
                <h5><strong class="text-primary">RUA</strong><span class="text-danger">*</span> - tipo texto</h5>
                <h5><strong class="text-primary">NÚMERO</strong><span class="text-danger">*</span> - tipo texto</h5>

            </div>
            <div class="col-12 col-md-6">
                <h5><strong class="text-primary">BAIRRO</strong><span class="text-danger">*</span> - tipo texto</h5>
                <h5><strong class="text-primary">CIDADE</strong><span class="text-danger">*</span> - tipo texto</h5>
                <h5><strong class="text-primary">UF</strong><span class="text-danger">*</span> - tipo texto</h5>
                <h5><strong class="text-primary">CEP</strong><span class="text-danger">*</span> - tipo númerico</h5>
                <h5><strong class="text-primary">COMPLEMENTO</strong>- tipo texto</h5>
                <h5><strong class="text-primary">TELEFONE</strong><span class="text-danger">*</span> - tipo númerico</h5>
                <h5><strong class="text-primary">EMAIL</strong> - tipo texto</h5>

            </div>
        </div>
        <div class="col-12 col-md-2">
            <a href="{{ route('clientes.import-download') }}" class="btn btn-primary">
                <i class="ri-file-download-line"></i>
                Download Modelo
            </a>
        </div>
    </div>
    <div class="card-footer">
        <hr>
        <form id="form-import" class="row" method="post" action="{{ route('clientes.import-store') }}" enctype="multipart/form-data">
            @csrf
            <p>Importar modelo preenchido</p>
            <div class="form-group validated col-12 col-lg-6">
                <label class="col-form-label">.xls/.xlsx</label>
                <div class="">
                    <span class="btn btn-success btn-file">
                        <i class="ri-file-search-line"></i>
                        Procurar arquivo<input accept=".xls, .xlsx" name="file" type="file" id="file">
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript">
    $('#file').change(function() {
        $('#form-import').submit();
        $body = $("body");
        $body.addClass("loading");
        
    });
</script>
@endsection
