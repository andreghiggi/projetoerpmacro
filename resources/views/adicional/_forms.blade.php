<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('categoria_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    @if(__isInternacionalizar(Auth::user()->empresa))
    <div class="col-md-3">
        {!!Form::text('nome_en', 'Nome (em inglês)')
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('nome_es', 'Nome (em espanhol)')
        !!}
    </div>
    @endif
    
    <div class="col-md-2">
        {!!Form::tel('valor', 'Valor')
        ->required()
        ->value(isset($item) ? __moeda($item->valor) : '')
        ->attrs(['class' => 'moeda'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>