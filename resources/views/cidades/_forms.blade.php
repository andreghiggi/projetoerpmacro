<div class="row g-2">
    <div class="col-md-4">
        {!!Form::text('nome', 'Nome')
        ->attrs(['class' => 'form-control'])
        ->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::tel('codigo', 'Código')
        ->required()
        ->attrs(['data-mask' => '00000000'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('uf', 'UF')
        ->required()
        ->attrs(['data-mask' => 'AA'])
        !!}
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
