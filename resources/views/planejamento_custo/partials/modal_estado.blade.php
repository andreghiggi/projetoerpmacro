<div class="modal fade" id="modal_estado" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        {!!Form::open()
        ->post()
        ->route('planejamento-custo.alterar-estado', [$item->id])
        !!}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Alterar Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mt-1">
                    <div class="col-md-4 col-12">
                        <h5 class="text-muted"><strong>Estado atual:</strong> <span class="ms-2">{!! $item->_estado() !!}</span></h5>
                    </div>
                </div>
                <div class="row g-2">

                    <div class="col-md-3">
                        {!!Form::select('estado_alterado', 'Novo estado', $item->proximosEstados())
                        ->attrs(['class' => 'form-select'])->required()
                        !!}
                    </div>

                    <div class="col-md-9">
                        {!!Form::text('observacao', 'Observação')
                        ->attrs(['class' => ''])
                        !!}
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-store">Salvar</button>
            </div>
        </div>
        {!!Form::close()!!}

    </div>
</div>