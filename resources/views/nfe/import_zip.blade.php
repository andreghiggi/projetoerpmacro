@extends('layouts.app', ['title' => 'Importar arquivo'])
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
        <h4>Importar arquivos xml para NFe</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('nfe.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    
    <div class="card-footer">
        <hr>
        <form id="form-import" class="row" method="post" action="{{ route('nfe.import-zip-store') }}" enctype="multipart/form-data">
            @csrf
            <p>Importar arquivo zip de xml</p>
            <div class="form-group validated col-12 col-lg-6">
                <label class="col-form-label">.zip</label>
                <div class="">
                    <span class="btn btn-success btn-file">
                        <i class="ri-file-search-line"></i>
                        Procurar arquivo<input accept=".zip" name="file" type="file" id="file">
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
