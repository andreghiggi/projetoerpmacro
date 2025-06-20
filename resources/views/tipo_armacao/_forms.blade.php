<div class="row g-2">
    
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')->required()
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::select('status', 'Status', [1 => 'Sim', 0 => 'Não'])->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>