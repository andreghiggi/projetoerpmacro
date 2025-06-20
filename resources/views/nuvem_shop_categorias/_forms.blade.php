<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')
        ->value(isset($item) ? $item->name->pt : '')
        ->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('categoria_id', 'Atribuir a categoria (opcional)', ['' => 'Selecione'] + $categorias)
        ->value(isset($item) ? $item->parent : '')
        ->attrs(['class' => 'select2'])
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::textarea('descricao', 'Descrição')
        ->value(isset($item) ? $item->description->pt : '')
        !!}
    </div>

  
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>