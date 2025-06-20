<div id="cpf_nota" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">CPF na Nota?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::tel('cliente_cpf_cnpj', 'CPF/CNPJ')->attrs(['class' => 'cpf_cnpj']) !!}
                    </div>
                    <div class="col-md-12 mt-3">
                        {!! Form::text('cliente_nome', 'Nome (opcional)')->attrs(['class' => '']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_fiscal" class="btn btn-primary" data-bs-dismiss="modal">Emitir</button>
            </div>
        </div>
    </div>
</div>
