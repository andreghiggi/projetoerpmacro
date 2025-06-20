<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('margem', 'Margem')->attrs(['class' => 'percentual'])->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('percentual', 'Percentual de comissão')->attrs(['class' => 'percentual'])->required()
        !!}
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
