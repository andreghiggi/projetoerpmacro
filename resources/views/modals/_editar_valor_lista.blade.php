<div class="modal fade" id="editar_valor_lista" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Produto <strong id="produto-nome"></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {!!Form::open()
                    ->post()
                    ->route('lista-preco.update-item')
                    !!}
                    <input type="hidden" name="item_id" value="" id="item_id">
                    <div class="col-md-6 mt-3">
                        {!! Form::tel('valor', 'Valor')->attrs(['class' => 'moeda']) !!}
                    </div>

                    <div class="mt-3 ms-auto">
                        <button type="submit" class="btn btn-primary px-3 float-end">Salvar</button>
                    </div>
                    {!!Form::close()!!}
                </div>
            </div>
           
        </div>
    </div>
</div>
