<div class="modal fade" id="modal_edita_descarregamento" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Editar descarregamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        {!! Form::select(
                        'tp_unid_transp_modal',
                        'Tipo unidade de transporte', ['' => 'Selecione...'] +
                        App\Models\Mdfe::tiposUnidadeTransporte(),
                        )->attrs(['class' => 'form-select']) !!}
                    </div>

                    <div class="col-md-3">
                        {!! Form::tel('id_unid_transp_modal', 'ID da Unidade de transporte (placa)')->attrs(['class' => 'placa']) !!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::tel('quantidade_rateio_modal', 'Quantidade de rateio (transporte)')->attrs(['data-mask' => '000,00']) !!}
                    </div>
                    
                    <div class="col-md-3">
                        {!! Form::tel('quantidade_rateio_carga_modal', 'Quantidade de rateio (unidade carga)')->attrs(['data-mask' => '000,00', 'class' => 'ignore']) !!}
                    </div>

                    <div class="col-md-12">
                        {!! Form::tel('chave_nfe_modal', 'NFe referência')->attrs(['class' => 'ignore chave_nfe']) !!}
                    </div>

                    <div class="col-md-12">
                        {!! Form::tel('chave_cte_modal', 'CTe referência')->attrs(['class' => 'ignore chave_nfe']) !!}
                    </div>

                    <div class="col-md-12">
                        {!! Form::select('municipio_descarregamento_modal', 'Município', ['' => 'Selecione...'] + $cidades->pluck('info', 'id')->all())->attrs(['class' => 'select2']) !!}
                    </div>

                    <div class="col-md-3">
                        <label>Lacres de transporte</label>
                        <div class="lascres_de_transporte">
                            
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label>Lacres de unid. carga</label>
                        <div class="lascres_de_unidade">
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="text-end">
                    <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Sair</button>
                    <button type="button" class="btn btn-success" id="btn-save-descarregamento">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</div>
