<div class="modal fade" id="finalizar_troca" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Finalizar Troca</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div> 
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-info" id="btn-comprovante-troca" style="height: 50px; width: 100%">
                            <i class="bx bx-file-blank"> </i> GERAR COMPROVANTE
                        </button>
                    </div>
                    
                </div>
            </div>
        </div> 
    </div> 
</div> 
@include('modals._cpf_nota', ['not_submit' => true])
