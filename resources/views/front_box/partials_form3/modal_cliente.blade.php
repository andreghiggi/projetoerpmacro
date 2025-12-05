<div class="modal fade modal-cliente" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Selecione o cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::select('cliente_id', 'Cliente', ["" => "Selecione"] + $clientes->pluck('info', 'id')->all())
                        ->value(isset($item) ? $item->cliente_id : (isset($clientePadrao) && $clientePadrao ? $clientePadrao->id : null))
                        ->id('cliente')
                        ->attrs(['class' => 'select-cliente']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-seleciona-cliente" data-bs-dismiss="modal">Selecionar</button>
            </div>
        </div>
    </div>
</div>
