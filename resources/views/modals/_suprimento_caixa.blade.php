<div class="modal fade modal-action-pos" id="suprimento_caixa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="staticBackdropLabel">
                    <i class="ri-arrow-down-line"></i> Suprimento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!!Form::open()
                ->post()
                ->route('suprimento.store')
                !!}
                <div class="row">

                    @isset($abertura)
                    <input type="hidden" name="caixa_id" value="{{ $abertura->id }}">
                    @else

                    @isset($item)
                    <input type="hidden" name="caixa_id" value="{{ $item->id }}">
                    @endif
                    @endif
                    <div class="col-md-6 mt-2">
                        {!! Form::tel('valor', 'Valor')->attrs(['class' => 'moeda'])->required() !!}
                    </div>
                    
                    <div class="col-md-12 mt-2 div-conta-empresa">
                        {!!Form::select('conta_empresa_suprimento_id', 'Conta empresa')
                        ->attrs(['class' => 'conta_empresa'])
                        ->required()
                        !!}
                    </div>

                    <div class="col-md-6 mt-2">
                        {!!Form::select('tipo_pagamento', 'Tipo de pagamento', App\Models\Nfce::tiposPagamento())
                        ->attrs(['class' => 'form-select'])
                        ->required()
                        !!}
                    </div>
                    <div class="col-md-12 mt-2">
                        {!! Form::textarea('observacao', 'Observação')
                        ->attrs(['row' => '4']) !!}
                    </div>
                </div>

                <div class="mt-3 ms-auto">
                    <button type="submit" class="btn btn-success px-3 float-end">
                        <i class="ri-checkbox-circle-line"></i> Salvar Suprimento
                    </button>
                </div>
                {!!Form::close()!!}
            </div>

        </div>
    </div>
</div>
