<div class="modal fade modal-action-pos" id="sangria_caixa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="staticBackdropLabel">
                    <i class="ri-arrow-up-line"></i>
                    Sangria
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!!Form::open()
                ->post()
                ->route('sangria.store')
                !!}
                <div class="row">
                    @isset($abertura)
                    <input type="hidden" name="caixa_id" value="{{ $abertura->id }}">
                    @else

                    @isset($item)
                    <input type="hidden" name="caixa_id" value="{{ $item->id }}">
                    @endif
                    @endif
                    <div class="col-md-6">
                        {!! Form::tel('valor', 'Valor')->attrs(['class' => 'moeda'])->required() !!}
                    </div>
                    <div class="col-md-12 div-conta-empresa">
                        {!!Form::select('conta_empresa_sangria_id', 'Conta empresa')
                        ->attrs(['class' => 'conta_empresa'])
                        ->required()
                        !!}
                    </div>

                    <div class="col-md-12 mt-2">
                        {!! Form::textarea('observacao', 'Observação')->attrs(['rows' => '3']) !!}
                    </div>
                    <div class="mt-3 ms-auto">
                        <button type="submit" class="btn btn-danger px-3 float-end">
                            <i class="ri-checkbox-circle-line"></i> Salvar Sangria
                        </button>
                    </div>
                </div>
                {!!Form::close()!!}
            </div>

        </div>
    </div>
</div>
