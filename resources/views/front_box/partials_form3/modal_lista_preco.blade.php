<div class="modal fade modal-lista-preco" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mt-2">
                        {!! Form::select('lista_preco_id', 'Lista de preço', ['' => 'Selecione'] + $listasPreco->pluck('nome', 'id')->all())->attrs(['class' => 'form-select']) !!}
                    </div>
                
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-selecionar-lista" data-bs-dismiss="modal">Selecionar</button>
            </div>
        </div>
    </div>
</div>
