<div class="row g-2">

    <div class="col-md-4">
        {!!Form::text('nome', 'Nome')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Status', ['AVAILABLE' => 'Ativo', 'UNAVAILABLE' => 'Desativado'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('template', 'Template', ['DEFAULT' => 'Padrão', 'PIZZA' => 'Pizza'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>


    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5 ">
            <span class="spinner-grow spinner-grow-sm d-none" role="status" aria-hidden="true"></span>
            Salvar
        </button>
    </div>
</div>