<div class="modal fade" id="modal_novo_fornecedor" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Novo Fornecedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        {!!Form::text('novo_cpf_cnpj', 'CPF/CNPJ')->attrs(['class' => 'cpf_cnpj'])->required()
                        !!}
                    </div>
                    <div class="col-md-4">
                        {!!Form::text('novo_razao_social', 'Razão Social')->attrs(['class' => ''])->required()
                        !!}
                    </div>
                    <div class="col-md-4">
                        {!!Form::text('novo_nome_fantasia', 'Nome Fantasia')->attrs(['class' => 'ignore'])
                        !!}
                    </div>
                    <div class="col-md-2">
                        {!!Form::text('novo_ie', 'IE')->attrs(['class' => 'ie ignore'])
                        !!}
                    </div>
                    <div class="col-md-2">
                        {!!Form::tel('novo_telefone', 'Telefone')->attrs(['class' => 'fone'])->required()
                        !!}
                    </div>
                    <div class="col-md-2">
                        {!!Form::select('novo_contribuinte', 'Contribuinte', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])
                        !!}
                    </div>
                    <div class="col-md-2">
                        {!!Form::select('novo_consumidor_final', 'Consumidor Final', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])->required()
                        !!}
                    </div>
                    
                    <div class="col-md-4">
                        {!! Form::text('novo_email', 'Email')->attrs(['class' => 'ignore'])->type('email') !!}
                    </div>

                    <div class="col-md-4">
                        {!!Form::select('novo_cidade_id', 'Cidade')
                        ->attrs(['class' => 'select2'])
                        ->required()
                        !!}
                    </div>
                    <div class="col-md-3">
                        {!!Form::text('novo_rua', 'Rua')->attrs(['class' => ''])->required()
                        !!}
                    </div>
                    <div class="col-md-2">
                        {!!Form::text('novo_numero', 'Número')->attrs(['class' => ''])->required()
                        !!}
                    </div>
                    <div class="col-md-3">
                        {!!Form::text('novo_cep', 'CEP')->attrs(['class' => 'cep'])->required()
                        !!}
                    </div>
                    <div class="col-md-3">
                        {!!Form::text('novo_bairro', 'Bairro')->attrs(['class' => ''])->required()
                        !!}
                    </div>
                    <div class="col-md-5">
                        {!!Form::text('novo_complemento', 'Complemento')->attrs(['class' => 'ignore'])
                        !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-store-fornecedor">Salvar</button>
            </div>
        </div>
    </div>
</div>