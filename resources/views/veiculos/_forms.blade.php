<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('placa', 'Placa')->required()
        ->attrs(['class' => 'placa'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('uf', 'UF', App\Models\Cidade::estados())
        ->attrs(['class' => 'form-select select2'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('cor', 'Cor')->required()
        ->attrs(['data-mask' => 'AAAAAAAAAA'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('marca', 'Marca')->required()
        ->attrs(['data-mask' => 'AAAAAAAAAAAAAAAAAAA'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('modelo', 'Modelo')->required()
        ->attrs(['data-mask' => 'AAAAAAAAAAAAAAAAAAA'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('rntrc', 'RNTRC')->required()
        ->attrs(['data-mask' => '00000000'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('renavam', 'Renavam')
        ->attrs(['data-mask' => '000000000000'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('taf', 'TAF')
        ->attrs(['data-mask' => '00000000000000'])
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('numero_registro_estadual', 'Nº registro estadual')
        ->attrs(['data-mask' => '0000000000000000000000000'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo', 'Tipo', ['' => 'Selecione'] + App\Models\Veiculo::tipos())->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_carroceria', 'Tipo carroceria', ['' => 'Selecione'] + App\Models\Veiculo::tiposCarroceria())
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_rodado', 'Tipo rodado', ['' => 'Selecione'] + App\Models\Veiculo::tiposRodado())
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('tara', 'Tara')->required()
        ->attrs(['data-mask' => '0000000000'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('capacidade', 'Capacidade')->required()
        ->attrs(['data-mask' => '0000000000'])
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('proprietario_nome', 'Nome do proprietário')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('proprietario_documento', 'CPF/CNPJ')->required()
        ->attrs(['class' => 'cpf_cnpj'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('proprietario_ie', 'IE')
        ->attrs(['class' => 'ie_rg'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('proprietario_uf', 'UF proprietário', App\Models\Cidade::estados())
        ->attrs(['class' => 'form-select select2'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('proprietario_tp', 'Tipo do proprietário', App\Models\Veiculo::tiposProprietario())
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('funcionario_id', 'Motorista', ['' => 'Selecione'] + $funcionarios->pluck('nome', 'id')->all())
        ->attrs(['class' => 'select2'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <hr>
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
