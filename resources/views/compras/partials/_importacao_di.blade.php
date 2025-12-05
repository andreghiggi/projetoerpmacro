<div class="modal fade" id="modal_importacao_di" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Dados do Importação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">

                    <div class="col-md-12">
                        <h4 class="nome"></h4>
                    </div>
                    <div class="col-md-2">
                        {!! Form::tel('nDI', 'Núm. importação')->attrs(['class' => '']) !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::date('dDI', 'Data importação')->attrs(['class' => '']) !!}
                    </div>

                    <div class="col-md-4">
                        {!! Form::select('cidade_desembarque_id', 'Cidade de desembarque', ['' => 'Selecione'] + $cidades->pluck('info', 'id')->all())
                        ->attrs(['class' => 'select2']) !!}
                    </div>

                    <div class="col-md-2">
                        {!! Form::date('dDesemb', 'Data de desembarque')->attrs(['class' => '']) !!}
                    </div>

                    <div class="col-md-2">
                        {!! Form::tel('vAFRMM', 'Valor da AFRMM')->attrs(['class' => 'moeda']) !!}
                    </div>

                    <div class="col-md-4">
                        {!! Form::select('tpViaTransp', 'Tipo de Via de Transporte', ['' => 'Selecione'] + \App\Models\Nfe::getTiposViaTransp())
                        ->attrs(['class' => 'select2']) !!}
                    </div>

                    <div class="col-md-3">
                        {!! Form::select('tpIntermedio', 'Tipo de intermediário', ['' => 'Selecione'] + \App\Models\Nfe::getTiposIntermedio())
                        ->attrs(['class' => 'select2']) !!}
                    </div>

                    <div class="col-md-3">
                        {!! Form::tel('cpf_cnpj_di', 'CPF/CNPJ')->attrs(['class' => 'cpf_cnpj']) !!}
                    </div>

                    <div class="col-md-2">
                        {!! Form::select('UFTerceiro', 'UF de terceiro', ['' => 'Selecione'] + \App\Models\Cidade::estados())
                        ->attrs(['class' => 'select2']) !!}
                    </div>

                    <div class="col-md-2">
                        {!! Form::text('cExportador', 'Código do exportador')->attrs(['class' => '']) !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::text('nAdicao', 'Número de adição')->attrs(['class' => '']) !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::text('cFabricante', 'Código do fabricante')->attrs(['class' => '']) !!}
                    </div>

                    <div class="col-md-2">
                        {!! Form::tel('vBCII', 'VBC Importação')->attrs(['class' => 'moeda']) !!}
                    </div>

                    <div class="col-md-2">
                        {!! Form::tel('vDespAdu', 'Valor aduaneiro')->attrs(['class' => 'moeda']) !!}
                    </div>

                    <div class="col-md-2">
                        {!! Form::tel('vII', 'Valor Importação')->attrs(['class' => 'moeda']) !!}
                    </div>

                    <div class="col-md-2">
                        {!! Form::tel('vIOF', 'Valor IOF')->attrs(['class' => 'moeda']) !!}
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary salvar-dados-importacao" data-bs-dismiss="modal">Salvar</button>
            </div>
        </div>
    </div>
</div>
