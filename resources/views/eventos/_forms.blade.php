<div class="row g-2">
    <div class="col-md-4">
        {!!Form::text('nome', 'Nome')
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('tipo', 'Tipo', ['mensal' => 'Mensal', 'anual' => 'Anual', 'semanal' => 'Semanal'])->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('metodo', 'Método', ['fixo' => 'Fixo', 'informado' => 'Informado'])->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('condicao', 'Condição', ['soma' => 'Soma', 'diminui' => 'Diminui'])->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('tipo_valor', 'Tipo Valor', ['fixo' => 'Valor Fixo', 'percentual' => 'Percentual'])->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('ativo', 'Ativo', ['1' => 'Sim', '0' => 'Não'])->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>