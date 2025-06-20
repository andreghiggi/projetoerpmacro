@extends('layouts.app', ['title' => 'Importação de retorno'])
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
        <h4>Importação de retorno</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('remessa-boleto.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->id('form-import')
        ->post()
        ->route('remessa-boleto.import-store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            <p>Importar modelo preenchido</p>
            <div class="form-group validated col-12 col-lg-6">
                <label class="col-form-label">.ret</label>
                <div class="">
                    <span class="btn btn-success btn-file">
                        <i class="ri-file-search-line"></i>
                        Procurar arquivo<input accept=".ret, .RET" name="file" type="file" id="file">
                    </span>
                </div>
            </div>
        </div>
        {!!Form::close()!!}
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
