<div class="modal fade modal-fatura" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Fatura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div> 
            <div class="modal-body">

                <h3>Total <strong class="total-fatura text-primary">R$ 0,00</strong></h3>

                <div class="row">
                    <table class="table mb-0 table-striped table-dynamic">
                        <thead>
                            <tr>
                                <th>Tipo de pagamento</th>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
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
                                    <input type="date" class="form-control data_vencimento" name="data_vencimento_row[]">
                                </td>
                                <td width="150">
                                    <input type="tel" class="form-control moeda valor_integral_row" name="valor_integral_row[]">
                                </td>

                                <td width="30">
                                    <button class="btn btn-sm btn-danger btn-remove-tr">
                                        <i class="ri-delete-back-2-line"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-12">
                        <br>
                        <button type="button" class="btn btn-dark btn-add-tr">
                            <i class="ri-add-line"></i>
                            Adicionar
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <h4>Soma fatura <strong class="soma-fatura text-muted">R$ 0,00</strong></h4>
                    <h4>Total faltante <strong class="total-faltante text-danger">R$ 0,00</strong></h4>
                    <p class="text-danger">Soma dos pagamentos deve ser igual ao total</p>
                </div>
            </div>
            <div class="modal-footer">
                <button disabled type="button" class="btn btn-success salvar-fatura" data-bs-dismiss="modal">Salvar</button>
            </div>

        </div> 
    </div> 
</div> 

