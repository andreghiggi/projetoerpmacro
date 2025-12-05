<div class="modal fade modal-frete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div> 
            <div class="modal-body">
                <div class="row m-2">

                    <div class="col-12">
                        {!! Form::text('valor_frete', 'Valor de frete')
                        ->attrs(['class' => 'moeda']) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success salvar-frete" data-bs-dismiss="modal">Salvar</button>
                </div>
            </div> 
        </div> 
    </div> 
</div> 
