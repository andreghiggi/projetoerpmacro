<div class="modal fade modal-cartao" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Dados do cartão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::select('bandeira_cartao', 'Bandeira', ["" => "Selecione"] + App\Models\Nfce::bandeiras())
                        ->attrs(['class' => 'form-select']) !!}
                    </div>
                    <div class="col-md-6 mt-3">
                        {!! Form::tel('cAut_cartao', 'Código autorização (opcional)')->attrs(['class' => '']) !!}
                    </div>
                    <div class="col-md-6 mt-3">
                        {!! Form::tel('cnpj_cartao', 'CNPJ (opcional)')->attrs(['class' => 'cnpj']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-modal-finalizar" data-bs-dismiss="modal">Salvar</button>
            </div>
        </div>
    </div>
</div>
