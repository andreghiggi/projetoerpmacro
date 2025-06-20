<div class="modal fade modal-cashback" id="_cashback" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Cash Back</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div> 
            <div class="modal-body">
                <div class="row m-2">
                    <div class="cashback-div row">
                        <p class="info_cash_back text-success"></p>

                        <div class="col-12">
                            <p>Valor de cashback disponível para uso: <strong class="text-success valor-cashback-disponivel">R$ 0,00</strong></p>

                        </div>

                        <div class="col-12 col-md-3">
                            {!! Form::text('valor_cashback', 'Valor de cashback')
                            ->attrs(['class' => 'moeda']) !!}
                        </div>

                        <div class="col-12 col-md-3">
                            {!! Form::select('permitir_credito', 'Permitir crédito', ['1' => 'Sim', '0' => 'Não'])
                            ->attrs(['class' => 'form-select']) !!}
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success cliente-venda" data-bs-dismiss="modal">Selecionar</button>
            </div>
        </div>
    </div>
</div>