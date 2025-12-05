<div class="row g-2">
    <div class="col-md-2">
        {!!Form::tel('valor_recebido[]', 'Valor')
        ->attrs(['class' => 'moeda'])
        ->value(__moeda($item->valor_integral))
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('data_recebimento[]', 'Data do Recebimento')
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_pagamento[]', 'Tipo de Pagamento', ['' => 'Selecione'] + App\Models\ContaReceber::tiposPagamento())
        ->attrs(['class' => 'form-select'])
        ->required()
        ->value($item->tipo_pagamento)
        !!}
    </div>

    <div class="col-md-3 div-conta-empresa">
        {!!Form::select('conta_empresa_id[]', 'Conta empresa')
        ->required()
        ->attrs(['class' => 'conta_empresa'])
        ->id('conta_empresa_'.$key)
        !!}
    </div>
    <input type="hidden" value="{{ $item->id }}" name="conta_id[]">
</div>
<hr>
