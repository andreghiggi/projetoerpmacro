<div class="row">
    <div class="col-md-3">
        {!!Form::text('name', 'Nome')
        ->required()
        ->attrs(['maxlength' => 50])!!}
    </div>
    <div class="col-md-6">
        {!!Form::text('description', 'Descrição')
        ->required()
        ->attrs(['maxlength' => 100])!!}
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
