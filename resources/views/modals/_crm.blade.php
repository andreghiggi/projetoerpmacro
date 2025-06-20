<div class="modal fade" id="modal_crm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Novo Registro CRM</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                {!!Form::open()
                ->post()
                ->route('crm.store')
                ->id('form-crm')
                !!}
                <div class="row g-2">

                    <input type="hidden" name="cliente_id" id="cliente_id" value="">
                    <input type="hidden" name="fornecedor_id" id="fornecedor_id" value="">
                    <div class="col-md-4">
                        {!!Form::text('assunto', 'Assunto')->required()
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::select('status', 'Status', ['' => 'Selecione'] + App\Models\CrmAnotacao::getStatus())
                        ->attrs(['class' => 'form-select'])
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::select('alerta', 'Alerta', [1 => 'Sim', 0 => 'Não'])
                        ->attrs(['class' => 'form-select'])
                        ->required()
                        !!}
                    </div>

                    <div class="col-md-4">
                        {!!Form::select('funcionario_id', 'Vendedor')
                        ->options(isset($item) && $item->funcionario ? [$item->funcionario->id => $item->funcionario->info] : [])
                        !!}
                    </div>

                    <div class="col-md-3">
                        {!!Form::date('data_retorno', 'Data de retorno')
                        !!}
                    </div>

                    <div class="col-md-3">
                        {!!Form::date('data_entrega', 'Data de entrega')
                        !!}
                    </div>

                    <div class="col-md-3">
                        {!!Form::select('conclusao', 'Conclusão', ['' => 'Selecione'] + App\Models\CrmAnotacao::getConclusoes())
                        ->attrs(['class' => 'form-select'])
                        !!}
                    </div>

                    <div class="col-md-3">
                        {!!Form::select('tipo_registro', 'Referênciar', ['' => 'Selecione', 'venda' => 'Venda', 'compra' => 'Compra', 'orçamento' => 'Orçamento'])
                        ->attrs(['class' => 'form-select'])
                        !!}
                    </div>

                    <div class="col-md-6 d-none d-registro">
                        {!!Form::select('registro_id', 'Registro de venda/compra/orçamento')
                        ->attrs(['class' => 'form-select'])
                        !!}
                    </div>
                    
                    <div class="mt-3 ms-auto">
                        <button type="submit" class="btn btn-success px-3 float-end btn-save">Salvar</button>
                    </div>
                </div>
                {!!Form::close()!!}

            </div>

        </div>
    </div>
</div>
