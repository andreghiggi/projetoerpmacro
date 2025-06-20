@section('css')
<style type="text/css">
    input[type="file"] {
        display: none;
    }

    .file label {
        padding: 8px 8px;
        width: 100%;
        background-color: #27BCC2;
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
<div class="row g-2">
    <div class="col-md-2">
        {!!Form::select('uf', 'UF', ['' => 'Selecione'] + \App\Models\Cidade::estados())
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('versao', 'Versão')->required()
        !!}
    </div>

    <div class="col-md-2 file">
        {!!Form::file('file', 'Arquivo')->required()
        ->attrs(['accept' => '.csv'])
        !!}
    </div>

    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5 ">
            <span class="spinner-grow spinner-grow-sm d-none" role="status" aria-hidden="true"></span>
            Salvar
        </button>
    </div>
</div>

@section('js')
<script type="text/javascript">

    $('.btn-success').click(() => {
        setTimeout(() => {
            if($('#inp-versao').val() && $('#inp-uf').val() && $('#inp-file').val()){
                $('.spinner-grow').removeClass('d-none')
                $('.btn-success').attr('disabled', 1)
            }
        }, 100)
    })
</script>
@endsection
