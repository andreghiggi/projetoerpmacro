<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('descricao', 'Descrição')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('requisicao_segundos', 'Timeout de requisição')->required()
        !!}
    </div>
    
    <div class="col-md-12 mt-3">
        {!!Form::select('produtos[]', 'Produtos', $produtos->pluck('nome', 'id')->all())
        ->attrs(['class' => 'multi-select'])
        ->multiple()
        ->value(isset($item) ? $item->produtos->pluck('produto_id')->all() : [])
        ->required()!!}
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>