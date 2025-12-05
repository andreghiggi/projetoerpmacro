<div class="modal fade" id="lista_precos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white" id="staticBackdropLabel">Lista de preços</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        {!!Form::select('tipo_pagamento_lista', 'Tipo de pagamento', ['' => 'Selecione'] + App\Models\Nfce::tiposPagamento())
                        ->attrs(['class' => 'form-select'])
                        !!}
                    </div>

                    <div class="col-md-4">
                        {!! Form::select('funcionario_lista_id', 'Funcionário', ['' => 'Selecione'] + $funcionarios->pluck('nome', 'id')->all())
                        ->attrs(['class' => 'form-select'])
                        !!}
                    </div>

                    <div class="col-md-5">
                        {!! Form::select('lista_preco_id', 'Lista', ['' => 'Selecione'])
                        ->attrs(['class' => 'form-select'])
                        !!}
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="selecionaLista()" class="btn btn-dark btn-store" data-bs-dismiss="modal">
                    <i class="ri-checkbox-circle-line"></i>
                    Escolher lista
                </button>
            </div>
        </div>
    </div>
</div>