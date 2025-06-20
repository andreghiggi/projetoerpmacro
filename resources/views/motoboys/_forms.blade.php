<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('telefone', 'Telefone')->required()
        ->attrs(['class' => 'fone'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_comissao', 'Tipo comissão', ['valor_fixo' => 'Valor fixo', 'percentual' => 'Percentual'])->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor_comissao', 'Valor da comissão')->required()
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->valor_comissao) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Status', ['1' => 'Ativo', '0' => 'Desativado'])->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>