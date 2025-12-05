<div class="row">
    <div class="table-reponsive">
        <input type="hidden" id="confirma-itens" value="{{ ($config != null && $config->confirmar_itens_prevenda == 1) ? 1 : 0 }}">
        <div class="row">
            <div class="col-12 col-lg-6">
                <h5>Cliente: <strong style="color: steelblue">{{ $item->cliente_id ? $item->cliente->razao_social : 'Consumidor Final' }}</strong></h5>
            </div>
            <div class="col-6 col-lg-6">
                <h5>Data: <strong style="color: steelblue">{{ __data_pt($item->created_at, 1) }}</strong></h5>
            </div>
        </div>
        <div class="row">
            <div class="col-3 mt-1">
                {!! Form::select('gerar_conta_receber', 'Gerar Conta a Receber', [0 => 'Não', 1 => 'Sim'])
                ->value($config ? $config->gerar_conta_receber_padrao : 0)
                ->attrs(['class' => 'form-select']) !!}
            </div>
            <div class="col-3 mt-1">
                {!! Form::text('cpf_nota', "CPF/CNPJ na nota?")->attrs(['class' => 'cpf_cnpj']) !!}
            </div>
        </div>
        <hr>
        <h4 class="text-center mt-2">Itens</h4>
        <p class="mensagem-itens m-2 text-danger"></p>


        @if($config && $config->confirmar_itens_prevenda)
        <div class="col-md-4 mb-2">
            <div class="input-group input-group-merge">
                <div class="input-group-text" data-password="false">
                    <span class="ri-barcode-box-line"></span>
                </div>
                <input @if($item->status == 0) disabled @endif type="text" id="inp-codigo_barras" class="form-control">
            </div>
        </div>
        @endif

        <input type="hidden" id="pre_venda_id" name="pre_venda_id" value="{{ $item->id }}">
        <table class="table table-striped table-centered mb-0">
            <thead class="table-dark">
                <tr>
                    <th></th>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Valor</th>
                    @if($config != null && $config->confirmar_itens_prevenda == 1)
                    <th>Status</th>
                    @endif
                </tr>
            </thead>
            <tbody>

                @foreach ($item->itens as $i)
                <tr>
                    <td><img class="img-60" src="{{ $i->produto->img }}"></td>
                    <td class="produto_nome">{{ $i->produto->nome }}</td>
                    @if($i->produto->unidade == 'UN')
                    <td>{{ number_format($i->quantidade,0) }}</td>
                    @else
                    <td>{{ $i->quantidade }}</td>
                    @endif
                    <td>{{ __moeda($i->valor) }}</td>
                    @if($config != null && $config->confirmar_itens_prevenda == 1)
                    <td>
                        <input type="hidden" class="line_id" value="{{ $i->id }}">
                        <input type="hidden" class="line_status" value="0">
                        <input type="hidden" class="line_codigo_barras" value="{{ $i->produto->codigo_barras }}">

                        <button @if($item->status == 0) disabled @endif class="btn btn-sm btn-success confirma-item">
                            <i class="ri-check-line"></i>
                        </button>
                    </td>
                    @endif
                </tr>
                @endforeach

            </tbody>
        </table>
        <h5 class="m-2">TOTAL DE PRODUTOS: {{ __moeda($item->valor_total) }}</h5>
    </div>
    <hr class="mt-5">
    <div class="row">
        <div class="table-reponsive">
            <h4 class="text-center">Fatura</h4>
            <table class="table table-striped table-centered mb-0 table-dynamic">
                <thead class="table-dark">
                    <tr>
                        <th>Tipo Pagamento</th>
                        <th>Data Vencimento</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="" class="datatable-body">
                    @php $total = 0; @endphp
                    @if(sizeof($item->fatura) > 0)
                    @foreach ($item->fatura as $i)
                    <tr class="dynamic-form">
                        <td width="300">
                            <select name="tipo_pagamento[]" class="form-select tipo_pagamento">
                                <option value="">Selecione..</option>
                                @foreach(App\Models\Nfe::tiposPagamento() as $key => $c)
                                <option @if($i->tipo_pagamento == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td width="150">
                            <input value="{{ $i->vencimento }}" type="date" class="form-control" name="data_vencimento[]" id="">
                        </td>
                        <td width="150">
                            <input value="{{ __moeda($i->valor_parcela) }}" type="tel" class="form-control moeda valor_parcela" name="valor_fatura[]">
                        </td>
                        <td width="70">
                            <button class="btn btn-sm btn-danger btn-delete-row" @if($item->status == 0) disabled @endif>
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    </tr>
                    @php $total = $i->valor_parcela; @endphp

                    @endforeach
                    @else
                    <tr class="dynamic-form">
                        <td width="300">
                            <select name="tipo_pagamento[]" class="form-select tipo_pagamento">
                                <option value="">Selecione..</option>
                                @foreach(App\Models\Nfe::tiposPagamento() as $key => $c)
                                <option value="{{$key}}">{{$c}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td width="150">
                            <input required value="" type="date" class="form-control" name="data_vencimento[]" id="">
                        </td>
                        <td width="150">
                            <input value="" type="tel" class="form-control moeda valor_parcela" name="valor_fatura[]">
                        </td>
                        <td width="70">
                            <button @if($item->status == 0) disabled @endif class="btn btn-sm btn-danger btn-delete-row">
                                <i class="ri-delete-back-2-line"></i>
                            </button>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if($item->status == 1)
        <div class="row">
            <div class="col-12 m-1">
                <button type="button" class="btn btn-info btn-sm btn-add-tr px-5">
                    <i class="ri-add-circle-fill"></i>
                    Adicionar pagamento
                </button>
            </div>
        </div>
        @endif

        <div class="row">
            <h5 class="col-4 m-3">TOTAL DA FATURA: <strong class="total_parcelas"></strong></h5>
        </div>
    </div>
</div>
@if($item->status == 1)
<div class="modal-footer">
    @if($item->cliente_id != null)
    <button type="button" class="btn btn-primary btn-sbm" id="gerar_nfe">Gerar NFe</button>
    @endif

    <button type="button" class="btn btn-success btn-sbm" id="gerar_nfce">Gerar NFCe</button>

    <button type="button" class="btn btn-info finalizar_pre_venda btn-sbm">Somente Finalizar</button>
</div>
@endif
