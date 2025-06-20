<div class="modal fade" id="modal_credito" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Valor de crédito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-12">
                        {!!Form::text('valor_credito', 'Valor de crédito')->attrs(['class' => 'moeda'])
                        !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-usar-credito" class="btn btn-success">Usar Crédito</button>
            </div>
        </div>
    </div>
</div>