<div class="row g-2">
    @if(!isset($item))
    <div class="col-md-3">
        {!!Form::select('produto_id', 'Produto')->required()
        ->options(isset($item) ? [$item->produto_id => $item->produto->nome] : [])
        !!}
    </div>
    @else
    <div class="col-md-3">
        {!!Form::text('', 'Produto/Serviço')->disabled()
        ->value($item->produto ? $item->produto->nome : ($item->servico ? $item->servico->nome : ''))
        !!}
    </div>
    @endif

    <div class="col-md-2">
        {!!Form::tel('prazo_garantia', 'Prazo de garantia (dias)')->required()
        ->attrs(['data-mask' => '000000'])
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('cliente_id', 'Cliente')->required()
        ->options(isset($item) ? [$item->cliente_id => $item->cliente->info] : [])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('data_venda', 'Data da venda')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('data_solicitacao', 'Data da solicitação')->required()
        ->value(date('Y-m-d'))
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor_reparo', 'Valor do reparo')
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->valor_reparo) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Status', ['' => 'Selecione'] + \App\Models\Garantia::estados())->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-12">
        {!!Form::textarea('descricao_problema', 'Descrição do problema')
        ->attrs(['rows' => '4'])
        !!}
    </div>
    <div class="col-md-12">
        {!!Form::textarea('observacao', 'Observação')
        ->attrs(['rows' => '4'])
        !!}
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>