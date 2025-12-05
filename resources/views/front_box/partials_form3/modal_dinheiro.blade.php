<div class="modal fade modal-dinheiro" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Valor Recebido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div> 
            <div class="modal-body">

                <h3>Total <strong class="total-fatura text-primary">R$ 0,00</strong></h3>

                <div class="row">
                    <div class="col-md-6 mt-3">
                        {!! Form::tel('valor_recebido', 'Valor recebido')->attrs(['class' => 'moeda']) !!}
                    </div>
                </div>

                <div class="row mt-2">
                    <h4>Valor do troco <strong class="valor-troco text-muted">R$ 0,00</strong></h4>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success salvar-fatura" data-bs-dismiss="modal">OK</button>
            </div>

        </div> 
    </div> 
</div> 

