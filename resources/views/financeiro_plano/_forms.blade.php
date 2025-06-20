<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('valor', 'Valor')->attrs(['class' => 'moeda'])->required()
        ->value(__moeda($item->valor))
        !!}
    </div>

    <div class="col-md-3 mt-2">
        {!!Form::select('status_pagamento', 'Status de pagamento', \App\Models\FinanceiroPlano::statusDePagamentos())
        ->required()
        ->attrs(['class' => 'select2'])
        ->value($item->status_pagamento)
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('tipo_pagamento', 'Tipo de pagamento', \App\Models\Plano::formasPagamento())
        ->required()
        ->attrs(['class' => 'select2'])
        !!}
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
