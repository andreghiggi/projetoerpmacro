<div class="modal fade" id="modal_fatura_venda" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Gerar Fatura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-12">
                        <h5>Valor total: <strong class="lbl-total_fatura"></strong></h5>
                    </div>

                    <div class="col-md-3">
                        <label>Valor de entrada</label>
                        <input type="tel" class="form-control moeda" id="inp-entrada_fatura">
                    </div>

                    <div class="col-md-3">
                        <label>Quantidade de parcelas</label>
                        <input type="tel" class="form-control" data-mask="000" id="inp-parcelas_fatura">
                    </div>

                    <div class="col-md-3">
                        <label>Intervalo de vencimento</label>
                        <input type="tel" class="form-control" value="30" data-mask="000" id="inp-intervalo_fatura">
                    </div>

                    <div class="col-md-3">
                        <label>Primeiro vencimento</label>
                        <input type="date" class="form-control" id="inp-primeiro_vencimento_fatura">
                    </div>

                    <div class="col-md-4">
                        <label>Tipo de pagamento</label>
                        <select class="form-control form-select tipo_pagamento" id="inp-tipo_pagamento_fatura">
                            @foreach(App\Models\Nfe::tiposPagamento() as $key => $c)
                            <option value="{{$key}}">{{$c}}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-store-fatura">Gerar</button>
            </div>
        </div>
    </div>
</div>