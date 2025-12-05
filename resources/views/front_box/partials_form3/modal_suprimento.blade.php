<div class="modal fade modal-suprimento" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="staticBackdropLabel">
                    <i class="ri-arrow-down-line"></i> Suprimento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 mt-2">
                        {!! Form::tel('valor', 'Valor')->attrs(['class' => 'moeda'])->required() !!}
                    </div>
                    
                    <div class="col-md-6 mt-2">
                        {!!Form::select('tipo_pagamento', 'Tipo de pagamento', App\Models\Nfce::tiposPagamento())
                        ->attrs(['class' => 'form-select'])
                        ->required()
                        !!}
                    </div>
                    <div class="col-md-12 mt-2">
                        {!! Form::text('observacao', 'Observação')->attrs(['class' => '']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-salvar-suprimento" data-bs-dismiss="modal">
                    <i class="ri-checkbox-circle-line"></i>Salvar
                </button>
            </div>
        </div>
    </div>
</div>
