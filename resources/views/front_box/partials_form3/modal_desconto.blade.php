<div class="modal fade modal-desconto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div> 
            <div class="modal-body">
                <div class="row m-2">

                    <div class="col-12">
                        {!! Form::text('valor_desconto', 'Valor de desconto')
                        ->attrs(['class' => 'mask-num-up']) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success salvar-desconto" data-bs-dismiss="modal">Salvar</button>
                </div>
            </div> 
        </div> 
    </div> 
</div> 
