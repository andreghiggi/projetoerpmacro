@section('css')
<style type="text/css">
    .input_container {
        border: 1px solid #e5e5e5;
    }

    input[type=file]::file-selector-button {
        background-color: #fff;
        color: #000;
        border: 0px;
        border-right: 1px solid #e5e5e5;
        padding: 10px 15px;
        margin-right: 20px;
        transition: .5s;
    }

    input[type=file]::file-selector-button:hover {
        background-color: #eee;
        border: 0px;
        border-right: 1px solid #e5e5e5;
    }
</style>
@endsection
<div class="row g-2">
    <div class="col-md-2">
        {!!Form::select('departamento', 'Departamento', ['' => 'Selecione', 'financeiro' => 'Financeiro', 'suporte' => 'Suporte'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    <div class="col-md-6">
        {!!Form::text('assunto', 'Assunuto')->required()
        !!}
    </div>
    <div class="col-md-12">
        {!!Form::textarea('descricao', 'Descrição')
        ->attrs(['rows' => '10', 'class' => 'tiny'])
        !!}
    </div>

    <div class="col-md-6">
        <label>Anexo</label>
        <div class="input_container">
            {!!Form::file('anexo', '')!!}
        </div>
    </div>
    

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Enviar</button>
    </div>
</div>


