<div class="modal fade" id="modal-wpp-envio" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Envio de Fatura WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <h5 class="cliente_info"></h5>
                    <div class="col-md-3">
                        {!!Form::tel('telefone', 'Telefone')
                        ->attrs(['class' => 'fone'])
                        ->required()
                        !!}
                    </div>
                    <div class="col-md-12">
                        {!!Form::textarea('mensagem', 'Mensagem')
                        ->required()
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::checkbox('enviar_danfe', 'Enviar DANFE')
                        ->value(1)
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::checkbox('enviar_xml', 'Enviar XML')
                        ->value(1)
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::checkbox('enviar_pedido_a4', 'Enviar Pedido A4')
                        ->value(1)
                        !!}
                    </div>
                    <br>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-success btn-enviar-wpp">Enviar</button>
            </div>
        </div>
    </div>
</div>