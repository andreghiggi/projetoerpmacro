<div class="modal fade" id="modal_conta_pagar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Gerar Conta a Pagar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-2">
                        {!!Form::select('gerar_conta', 'Gerar conta a pagar', ['1' => 'Sim', '0' => 'Não'])->attrs(['class' => 'form-select'])->required()
                        !!}
                    </div>

                    <div class="col-md-4">
                        {!!Form::text('descricao', 'Descrição')
                        ->value('Pagamento de comissão')
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::text('valor_integral', 'Valor')->attrs(['class' => 'moeda'])
                        ->required()
                        !!}
                    </div>
                    <div class="col-md-2">
                        {!!Form::date('data_vencimento', 'Data Vencimento')
                        ->required()
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::select('status', 'Conta Paga', ['0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select'])->required()
                        !!}
                    </div>

                    <div class="col-md-3">
                        {!!Form::select('tipo_pagamento', 'Tipo Pagamento', App\Models\ContaReceber::tiposPagamento())->attrs(['class' => 'form-select'])->required()
                        !!}
                    </div>
                    <div class="col-md-6">
                        {!!Form::text('observacao', 'Observação')
                        !!}
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-store" data-bs-dismiss="modal">Salvar</button>
            </div>
        </div>
    </div>
</div>