<div class="row g-2">
    <div class="col-md-4">
        {!!Form::select('produto_id', 'Produto')->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('quantidade', 'Quantidade')
        ->attrs(['class' => 'qtd'])
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('estado', 'Estado', ['' => 'Selecione'] + App\Models\ItemInventario::estados())
        ->attrs(['class' => 'form-select'])
        ->required()
        !!}
    </div>

    <div class="col-md-12">
        {!!Form::textarea('observacao', 'Observação')->attrs(['rows' => '5'])
        !!}
    </div>
    
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>


