<div id="modal_finalizar_pdv2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Finalizando Venda <strong class="total-venda-modal text-success">R$ 0,00</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-dark px-5 btn-gerar-fatura mb-2">
                    <i class="ri-list-indefinite"></i>
                    Gerar Fatura
                </button>
                <div class="row g-2">
                    <div class="col-md-3">
                        {!! Form::tel('cliente_cpf_cnpj', 'CPF/CNPJ na nota? (opcional)')->attrs(['class' => 'cpf_cnpj']) !!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('cliente_nome', 'Nome (opcional)') !!}
                    </div>

                    <div class="col-md-6">
                        {!! Form::text('observacao', 'Observação (opcional)') !!}
                    </div>

                    <div class="col-12">
                        <hr>
                        <h4>Fatura</h4>
                        <div class="table-responsive">
                            <table class="table table-dynamic">
                                <thead>
                                    <tr>
                                        <th>Tipo de pagamento</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th>Observação</th>
                                        <th>Ação</th>
                                    </tr> 
                                </thead>
                                <tbody class="fatura">
                                    @if(isset($item) && isset($item->fatura))

                                    @foreach($item->fatura as $f)
                                    <tr class="dynamic-form">
                                        <td width="300">
                                            <select name="tipo_pagamento_row[]" class="form-control tipo_pagamento select2">
                                                <option value="">Selecione..</option>
                                                @foreach($tiposPagamento as $key => $c)
                                                <option @if($key == $f->tipo_pagamento) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150">
                                            <input type="date" class="form-control data_vencimento" name="data_vencimento_row[]" value="{{ $f->data_vencimento }}">
                                        </td>
                                        <td width="150">
                                            <input type="tel" class="form-control moeda valor_integral_row" name="valor_integral_row[]" value="{{ __moeda($f->valor) }}">
                                        </td>
                                        <td>
                                            <input type="text" name="obs_row[]" class="form-control ignore" value="{{ $f->observacao }}">
                                        </td>
                                        <td width="30">
                                            <button class="btn btn-sm btn-danger btn-remove-tr">
                                                <i class="ri-delete-back-2-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @else
                                    <tr class="dynamic-form">
                                        <td width="300">
                                            <select name="tipo_pagamento_row[]" class="form-control tipo_pagamento select2">
                                                <option value="">Selecione..</option>
                                                @foreach($tiposPagamento as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150">
                                            <input type="date" class="form-control data_vencimento" name="data_vencimento_row[]" value="{{ date('Y-m-d') }}">
                                        </td>
                                        <td width="150">
                                            <input type="tel" class="form-control moeda valor_integral_row" name="valor_integral_row[]" value="">
                                        </td>
                                        <td>
                                            <input type="text" name="obs_row[]" class="form-control ignore">
                                        </td>
                                        <td width="30">
                                            <button class="btn btn-sm btn-danger btn-remove-tr">
                                                <i class="ri-delete-back-2-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">Restante</td>
                                        <td colspan="1" class="total-restante"></td>
                                        <td colspan="2">
                                            <div class="col-md-6 d-troco d-none">
                                                <label>Troco</label>
                                                <input type="tel" class="form-control moeda troco" name="troco">
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-sm btn-primary btn-add-tr px-5">
                                    <i class="ri-add-fill"></i>
                                    Adicionar Pagamento
                                </button>
                            </div>
                        </div>

                        <div class="row p-cliente">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" id="btn_nao_fiscal" class="btn btn-primary">
                DOCUMENTO AUXILIAR</button>

                @can('nfce_create')
                <button type="button" id="btn_fiscal" class="btn btn-success">
                CUPOM FISCAL</button>
                @endcan
            </div>
        </div>
    </div>
</div>
