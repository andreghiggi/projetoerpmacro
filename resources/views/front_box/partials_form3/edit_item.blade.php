<div class="modal fade modal-edit-item" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div> 
            <div class="modal-body">
                <div class="row m-2">
                    <div class="col-12 col-lg-6">
                        {!! Form::tel('valor_unitario_edit', 'Valor Unitário')->attrs(['class' => 'moeda value_unit']) !!}
                    </div>

                    <div class="col-12 col-lg-6">
                        {!! Form::tel('quantidade_edit', 'Quantidade')->attrs(['class' => 'qtd']) !!}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success editar-item" data-bs-dismiss="modal">Salvar</button>
                </div>
            </div> 
        </div> 
    </div> 
</div> 
