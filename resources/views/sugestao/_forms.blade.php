<div class="row g-2">
    <div class="col-md-6">
        {!!Form::text('titulo', 'Título')->required()
        ->attrs(['maxLength' => 100])
        !!}
    </div>

    <div class="col-md-12">
        {!!Form::textarea('texto', 'Texto')
        ->attrs(['rows' => '10', 'class' => 'tiny'])
        !!}
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>