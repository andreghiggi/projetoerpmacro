<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('numero', 'Número')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('capacidade', 'Capacidade')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor_diaria', 'Valor da diária')->required()
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->valor_diaria) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('estacionamento', 'Estacionamento')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('categoria_id', 'Categoria', ['' => 'Selecione uma categoria'] + $categorias->pluck('nome', 'id')->all())->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-12">
        {!!Form::textarea('descricao', 'Descrição')
        ->attrs(['rows' => '6'])->required()
        !!}
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>