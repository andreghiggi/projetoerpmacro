<div id="pagamento_multiplo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header text-white bg-success">

                <h4 class="modal-title" id="standard-modalLabel">Pagamento Múltiplo <strong class="total-venda-modal text-danger">@isset($item) {{ __moeda($item->valor_total) }}@endif</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-dark px-5 btn-gerar-fatura mb-2">
                    <i class="ri-list-indefinite"></i>
                    Gerar Fatura
                </button>
                <div class="row">
                    <div class="col-md-4">
                        {!! Form::select('tipo_pagamento_row', 'Tipo de Pagamento',['' => 'Selecione'] + $tiposPagamento)->attrs(['class' => 'form-select']) !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::tel('valor_row', 'Valor')->attrs(['class' => 'moeda']) !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::date('data_vencimento_row', 'Vencimento')->attrs(['class' => ''])->value(date('Y-m-d')) !!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('observacao_row', 'Observação')->attrs(['class' => '']) !!}
                    </div>
                    <div class="col-md-1 mt-3">
                        <button type="button" style="margin-left: 15px" class="btn btn-info btn-add-payment"><i class="ri-add-line"></i></button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0 mt-2 table-payment">
                        <thead>
                            <tr>
                                <th>Tipo de Pagamento</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Observações</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($item)
                            @if($item != null && $item->fatura && sizeof($item->fatura) > 1)
                            @foreach ($item->fatura as $fatura)
                            <tr>
                                <td>
                                    <input readonly type="text" name="nome_pagamento[]" class="form-control"
                                    value="{{ App\Models\Nfce::getTipoPagamento($fatura->tipo_pagamento) }}">

                                    <input readonly type="hidden" name="tipo_pagamento_row[]" class="form-control"
                                    value="{{ $fatura->tipo_pagamento }}">
                                </td>
                                <td>
                                    <input readonly type="date" name="data_vencimento_row[]" class="form-control data_multiplo" value="{{ $fatura->data_vencimento }}">
                                </td>
                                <td>
                                    <input readonly type="text" name="valor_integral_row[]" class="form-control valor_integral" value="{{ __moeda($fatura->valor) }}">
                                </td>
                                <td>
                                    <input readonly type="text" name="obs_row[]" class="form-control" value="{{ $fatura->obs_row }}">
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger btn-delete-row">
                                        <i class="ri-delete-back-2-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                            @endisset
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Soma pagamento:</td>
                                @isset($data)
                                <td class="sum-payment">R$ {{ __moeda($data->valor_total) }}</td>
                                @else
                                <td class="sum-payment">R$ 0,00</td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="mt-2 text-danger">Valor restante: 
                                <strong class="sum-restante"></strong>
                            </h6>
                        </div>
                        <div class="col-md-6 div-troco-modal d-none">
                            <h6 class="mt-2 text-primary">Troco: 
                                <strong class="sum-troco"></strong>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-modal-multiplo" data-bs-dismiss="modal">
                    <i class="ri-checkbox-circle-line"></i>
                    Salvar
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
