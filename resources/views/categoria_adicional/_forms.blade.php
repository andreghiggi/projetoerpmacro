<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('minimo_escolha', 'Mínimo de escolhas')
        ->attrs(['data-mask' => '00'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('maximo_escolha', 'Máximo de escolhas')
        ->attrs(['data-mask' => '00'])
        !!}
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>