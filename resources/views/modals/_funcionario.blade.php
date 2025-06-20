<div class="modal fade modal-funcioario" id="funcionario" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Selecionar Vendedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div> 
            <div class="modal-body">
                <div class="col-12">
                    {!! Form::select('funcionario_id', 'Vendedor')
                    ->options(isset($funcionario) ? [$funcionario->id => $funcionario->nome] : [])
                    !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary funcionario-venda" data-bs-dismiss="modal">Salvar</button>
            </div>
        </div> 
    </div> 
</div>

