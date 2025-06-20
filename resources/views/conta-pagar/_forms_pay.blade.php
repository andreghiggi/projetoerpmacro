<div class="row g-2">
    <div class="col-md-2">
        {!!Form::tel('valor_pago', 'Valor')
        ->attrs(['class' => 'moeda'])
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('data_pagamento', 'Data do Pagamento')
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_pagamento', 'Tipo de Pagamento', ['' => 'Selecione'] + App\Models\ContaReceber::tiposPagamento())
        ->attrs(['class' => 'form-select'])
        ->required()
        ->value($item->tipo_pagamento)
        !!}
    </div>

    <div class="col-md-3 div-conta-empresa">
        {!!Form::select('conta_empresa_id', 'Conta empresa')
        ->required()
        !!}
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-success px-5">Pagar</button>
    </div>
</div>
