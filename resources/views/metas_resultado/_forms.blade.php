<div class="row g-2">
    
    <div class="col-md-3">
        {!!Form::select('funcionario_id', 'Funcionário')->required()
        ->options(isset($item) ? [$item->funcionario->id => $item->funcionario->nome] : [])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor', 'Valor')->attrs(['class' => 'moeda'])->required()
        ->value(isset($item) ? __moedaInput($item->valor) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tabela', 'Tabela', ['' => 'Selecione'] + \App\Models\MetaResultado::tabelas())->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>



