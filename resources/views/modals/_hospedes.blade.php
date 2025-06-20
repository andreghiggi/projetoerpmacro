<div class="modal fade" id="modal_hospedes" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Hóspedes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {!!Form::open()
            ->put()
            ->route('reservas.update-hospedes', [$item->id])
            !!}
            <div class="modal-body">
                <div class="row append">

                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success btn-store-cliente">Atualizar</button>
            </div>
            {!!Form::close()!!}

        </div>
    </div>
</div>
