<div class="row g-2">

    <div class="card">
        <div class="card-body">
            <div class="row g-2">

                <div class="col-md-4">
                    {!!Form::select('empresa_boleto', 'Empresa')->required()
                    !!}
                </div>
                <div class="col-12"></div>

                <div class="col-md-4">
                    {!!Form::text('razao_social', 'Razão social')->required()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::text('cpf_cnpj', 'CPF/CNPJ')->required()
                    ->attrs(['class' => 'cpf_cnpj'])
                    !!}
                </div>

                <div class="col-md-3">
                    {!!Form::text('email', 'Email')->required()
                    ->type('email')
                    !!}
                </div>

                <div class="col-md-3">
                    {!!Form::tel('telefone', 'Telefone')->required()
                    ->attrs(['class' => 'fone'])
                    !!}
                </div>

                <input type="hidden" name="plano_id" id="inp-plano_id">
                <div class="col-md-2">
                    {!!Form::text('plano', 'Plano')->readonly()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('valor', 'Valor')->required()
                    ->attrs(['class' => 'moeda'])
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('juros', 'Juros')->required()
                    ->attrs(['class' => 'percentual'])
                    ->value($config->percentual_juros_padrao_boleto)
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('multa', 'Multa')->required()
                    ->attrs(['class' => 'percentual'])
                    ->value($config->percentual_multa_padrao_boleto)
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::date('vencimento', 'Vencimento')->required()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('qtd_boletos', 'Qtd. de boletos')
                    ->attrs(['data-mask' => '00', 'class' => 'tooltipp2'])
                    !!}

                    <div class="text-tooltip2 d-none">
                        Preencher se precisar gerar mais de 1 boleto para o mês seguinte ao campo vencimento.
                    </div>
                </div>
                <hr>

                <div class="col-md-4">
                    {!!Form::text('rua', 'Rua')->required()
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::text('numero', 'Número')->required()
                    !!}
                </div>
                <div class="col-md-3">
                    {!!Form::text('bairro', 'Bairro')->required()
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::text('cep', 'CEP')->required()
                    ->attrs(['class' => 'cep'])
                    !!}
                </div>
            </div>
        </div>
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
