<div id="cpf_nota" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                @if(env("NFECNPJ") == 1)
                <h4 class="modal-title" id="standard-modalLabel">CPF na Nota?</h4>
                @else
                <h4 class="modal-title" id="standard-modalLabel">CPF/CNPJ na Nota?</h4>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-success btn-clinte">
                            <i class="ri-group-line"></i> Selecionar cliente
                        </button>
                        <p class="p-cliente"></p>
                    </div>
                    <div class="col-md-6 mt-2">
                        @if(env("NFECNPJ") == 1)
                        {!! Form::tel('cliente_cpf_cnpj', 'CPF (opcional)')->attrs(['class' => 'cpf']) !!}
                        @else
                        {!! Form::tel('cliente_cpf_cnpj', 'CPF/CNPJ (opcional)')->attrs(['class' => 'cpf_cnpj']) !!}
                        @endif
                    </div>
                    <div class="col-md-6 mt-2">
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
