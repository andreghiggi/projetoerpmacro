@extends('layouts.app', ['title' => 'Importar XML Devolução'])
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

    .card-body strong{
        color: #8833FF;
    }

</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Importar XML Devolução</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('devolucao.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('devolucao.store-xml')
        ->multipart()
        ->id('form-xml')
        !!}
        <div class="pl-lg-4">
            <div class="row">
                <div class="row m-2">
                    <div class="col-md-5 file-certificado">
                        {!! Form::file('file', 'XML')
                        ->attrs(['accept' => '.xml']) !!}
                        <span class="text-danger" id="filename"></span>
                    </div>

                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $('#inp-file').change(function() {
        $('#form-xml').submit();
    });
</script>
@endsection
