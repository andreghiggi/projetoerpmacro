<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')->required()
        !!}
    </div>
    
    <div class="col-md-4">
        {!!Form::select('cidade_id', 'Cidade')->required()
        ->options(isset($item) ? [$item->cidade_id => $item->cidade->info] : [])
        !!}
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>