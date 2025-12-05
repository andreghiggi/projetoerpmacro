<div class="modal fade modal-sangria" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">
                    <i class="ri-arrow-up-line"></i>
                    Sangria
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mt-2">
                        {!! Form::tel('valor', 'Valor')->attrs(['class' => 'moeda'])->required() !!}
                    </div>
                    
                    <div class="col-md-12 mt-2">
                        {!! Form::textarea('observacao', 'Observação')->attrs(['rows' => '4']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-salvar-sangria" data-bs-dismiss="modal">
                    <i class="ri-checkbox-circle-line"></i>
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>
