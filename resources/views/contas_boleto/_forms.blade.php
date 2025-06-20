<div class="row g-2">
    <div class="col-md-3">
        {!!Form::select('banco', 'Banco', ['' => 'Selecione'] + \App\Models\ContaBoleto::bancos())->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('agencia', 'Agência')->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('conta', 'Conta')->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('titular', 'Títular')->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('padrao', 'Padrão', [0 => 'Não', 1 => 'Sim'])->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('documento', 'CPF/CNPJ')->required()
        ->attrs(['class' => 'cpf_cnpj'])
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('rua', 'Rua')->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('numero', 'Número')->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('bairro', 'Bairro')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('cep', 'CEP')->required()
        ->attrs(['class' => 'cep'])
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::select('cidade_id', 'Cidade')->required()
        ->options(isset($item) ? [$item->cidade_id => $item->cidade->info] : [])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('carteira', 'Carteira')->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('convenio', 'Convênio')->required()
        !!}
    </div>

    <div class="col-md-1">
        {!!Form::tel('juros', 'Juros')
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->juros) : '')
        !!}
    </div>
    <div class="col-md-1">
        {!!Form::tel('multa', 'Multa')
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->multa) : '')
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('juros_apos', 'Juros após(dias)')
        ->attrs(['data-mask' => '000'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('tipo', 'Tipo', ['Cnab400' => 'Cnab400', 'Cnab240' => 'Cnab240'])->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>