<div class="row">
    <div class="col-md-3">
        {!!Form::select('tipo_pagamento', 'Tipo', App\Models\TaxaPagamento::tiposPagamento())
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('taxa', 'Taxa')->attrs(['class' => 'moeda'])->value(isset($item) ? __moeda($item->taxa) : '')
        ->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::select('bandeira_cartao', 'Tipo', ['' => 'Selecione'] + App\Models\TaxaPagamento::bandeiras())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
