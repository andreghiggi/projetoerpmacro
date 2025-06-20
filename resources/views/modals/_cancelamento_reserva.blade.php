<div class="modal fade" id="modal_cancelamento_reserva" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Cancelamento de reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!!Form::open()
                ->post()
                ->route('reservas.cancelamento', [$item->id])
                !!}
                <div class="row">

                    <div class="col-md-3">
                        {!! Form::tel('valor_total', 'Valor recebido da reserva')->attrs(['class' => 'moeda'])->required()
                        ->value(__moeda($item->valor_total)) !!}
                    </div>
                    

                    <div class="col-md-12 mt-2">
                        {!! Form::text('motivo_cancelamento', 'Motivo cancelamento')->attrs(['class' => ''])
                        ->required() !!}
                    </div>
                    <div class="mt-3 ms-auto">
                        <button type="submit" class="btn btn-danger px-3 float-end">Cancelar reserva</button>
                    </div>
                </div>
                {!!Form::close()!!}
            </div>

        </div>
    </div>
</div>
