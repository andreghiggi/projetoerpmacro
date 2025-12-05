<div class="modal fade" id="modal_fatura" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" action="{{ route('reservas.store-fatura', [$item->id]) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Dados de fatura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <h5>Valor total: <strong>R$ {{ __moeda($item->valor_total) }}</strong></h5>

                        <div class="row">
                            <div class="col-md-3">
                                <label>Desconto</label>
                                <input type="tel" class="form-control moeda" name="desconto" value="{{ __moeda($item->desconto) }}">
                            </div>
                            <div class="col-md-3">
                                <label>Acréscimo</label>
                                <input type="tel" class="form-control moeda" name="valor_outros" value="{{ __moeda($item->valor_outros) }}">
                            </div>

                            <div class="col-md-3">
                                <label>Gerar conta a receber</label>
                                <select class="form-control form-select" name="gerar_conta_receber">
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dynamic table-fatura">
                                <thead>
                                    <tr>
                                        <th>Tipo de Pagamento</th>
                                        <th>Data Vencimento</th>
                                        <th>Valor</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="body-pagamento" class="datatable-body">
                                    @if(sizeof($item->fatura) == 0)
                                    <tr class="dynamic-form">
                                        <td width="300">
                                            <select required name="tipo_pagamento[]" class="form-select">
                                                <option value="">Selecione</option>
                                                @foreach(App\Models\FaturaReserva::tiposPagamento() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150">
                                            <input required type="date" class="form-control" name="data_vencimento[]" id="" value="">
                                        </td>
                                        <td width="150">
                                            <input required type="tel" class="form-control moeda valor_fatura" name="valor[]" id="valor">
                                        </td>
                                        <td width="30">
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @else

                                    @foreach($item->fatura as $f)
                                    <tr class="dynamic-form">
                                        <td width="300">
                                            <select required name="tipo_pagamento[]" class="form-select">
                                                <option value="">Selecione</option>
                                                @foreach(App\Models\FaturaReserva::tiposPagamento() as $key => $c)
                                                <option @if($f->tipo_pagamento == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150">

                                            <input required type="date" class="form-control" name="data_vencimento[]" value="{{ $f->data_vencimento }}">
                                        </td>
                                        <td width="150">
                                            <input value="{{ __moeda($f->valor) }}" required type="tel" class="form-control moeda valor_fatura" name="valor[]" id="valor">
                                        </td>
                                        <td width="30">
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @endif

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">Soma da fatura</td>
                                        <td colspan="2" id="total_fatura">R$ {{ __moeda($item->fatura->sum('valor')) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-info btn-add-tr px-5">
                                    Adicionar parcela
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-store-categoria">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>