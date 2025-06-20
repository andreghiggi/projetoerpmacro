<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('modelo', 'Modelo')->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('acomodacao_id', 'Acomodação', ['' => 'Selecione'] + $acomodacoes->pluck('info', 'id')->all())
        ->attrs(['class' => 'select2'])
        ->required()
        !!}
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>