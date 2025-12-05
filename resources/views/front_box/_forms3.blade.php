
<div class="main">
    <div class="row header">
        <div class="col-lg-6">
            <h5>
                <a class="btn btn-sm btn-danger" href="{{ route('frontbox.index') }}">
                    <i class="ri-arrow-left-circle-fill"></i>
                </a> Empresa: <strong class="mr-1">{{ $empresa->nome }}</strong>
            </h5>
        </div>
        <div class="col-lg-2">
            <h5>
                Usuário: <strong>{{ $user->name }}</strong>
                <span class="badge bg-danger d-offline d-none" style="margin-left: 4px;">OFFLINE</span>
            </h5>

        </div>
        <div class="col-lg-4">
            <h5 style="float: right; margin-right: 10px;">
                <button type="button" onclick="vendaSuspensa()" class="btn btn-sm btn-primary">
                    <i class="ri-file-text-line"></i>
                    Vendas suspensas
                </button>
                
                <button type="button" class="btn btn-sm btn-dark" onclick="reiniciarVenda()">
                    <i class="ri-refresh-line"></i>
                    Reiniciar venda
                </button>

                <strong class="timer">{{ date('d/m/Y H:i:s') }}</strong>
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 grid1">
            <div class="row">

                <div class="col-12">
                    <label>Produto</label>
                    <select class="form-control select2" name="produto_id" id="produto_id">
                        <option value="">Selecione</option>
                        @foreach($produtos as $p)
                        <option data-valor_minimo_venda="{{ $p->valor_minimo_venda }}" data-valor="{{ $p->valor_unitario }}" value="{{ $p->id }}" @if($p->validaEstoque() == 0) disabled @endif>
                            #{{ $p->numero_sequencial }} 
                            @if(strlen($p->referencia) > 0)
                            REF: {{ $p->referencia }}
                            @endif

                            {{ $p->nome }} [R${{ __moeda($p->valor_unitario) }}]
                            @if($p->estoque)
                            Estoque: 

                            @if(!$p->unidadeDecimal())
                            {{ number_format($p->estoque->quantidade, 0, '.', '') }}
                            @else
                            {{ number_format($p->estoque->quantidade, 3, '.', '') }}
                            @endif
                            @endif

                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-lg-4 mt-2">
                    {!! Form::tel('valor_unitario', 'Valor Unitário')->attrs(['class' => 'moeda value_unit']) !!}
                </div>

                <div class="col-12 col-lg-4 mt-2">
                    {!! Form::tel('quantidade', 'Quantidade')->attrs(['class' => 'qtd']) !!}
                </div>

                <div class="col-12 col-lg-4 mt-2">
                    {!! Form::tel('sub_total', 'Subtotal')->attrs(['class' => 'moeda sub_total']) !!}

                </div>

                <div class="col-12 mt-2">
                    <button type="button" id="btn-adicionar" class="btn btn-primary w-100"><i class="ri-add-circle-fill"></i>
                    ADICIONAR</button>
                </div>
            </div>

            <div class="row div-itens">
                <div class="table-responsive">
                    <table class="table itens">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Vl. unit.</th>
                                <th>Qtd.</th>
                                <th>Subtotal</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-3">
                @if($isVendaSuspensa == 0)
                <button id="btn-suspender" type="button" class="btn btn-sm btn-light btn-suspender">
                    <i class="ri-timer-line"></i>Suspender venda
                </button>
                @else
                <a href="{{ route('frontbox.create') }}" class="btn btn-light btn-sm">
                    <i class="ri-refresh-line"></i>
                    Nova Venda
                </a>
                @endif
            </div>
            <div class="input-group mt-1">
                <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2">
                        <i class="ri-barcode-box-fill"></i>
                    </span>
                </div>
                <input id="inp-codigo-barras" type="text" class="form-control"
                placeholder="Buscar por código de barras [F1]" style="height: 36px;">
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-12 col-xl-6 col-lg-12 totalizador acoes-pdv">
                    Ações do PDV <span class="atalhos">[ctrl+a]</span>
                </div>
                <div class="col-12 col-lg-6 totalizador">
                    Soma dos produtos <strong class="soma-produtos">R$ 0,00</strong>
                </div>
                <div class="col-12 col-lg-6 totalizador mt-1">
                    Total de itens <strong class="total-itens">0</strong>
                </div>

                <div class="col-12 col-lg-6 totalizador mt-1 totalizador-desconto" onclick="openModalAcrescimo()">
                    Acréscimo 
                    <strong class="acrescimo">
                        R$ {{ isset($item) ? __moeda($item->acrescimo) : '0,00'}}
                    </strong>
                </div>
                <div class="col-12 col-lg-6 totalizador mt-1 totalizador-desconto" onclick="openModalDesconto()">
                    Desconto 
                    <strong class="desconto">
                        R$ {{ isset($item) ? __moeda($item->desconto) : '0,00'}}
                    </strong>
                </div>
                <div class="col-12 col-lg-6 totalizador mt-1 totalizador-valor_frete" onclick="openModalValorFrete()">
                    Valor de frete 
                    <strong class="valor_frete">
                        R$ {{ isset($item) ? __moeda($item->valor_frete) : '0,00'}}
                    </strong>
                </div>
                <div class="col-12 col-lg-6 totalizador mt-1 totalizador-observacao @if(isset($item) && strlen($item->observacao) > 1) text-primary @endif" onclick="openModalObservacao()">
                    Observação da venda
                </div>
                <div class="col-12 totalizador pagar mt-1">
                    Total a receber <strong class="total-venda">R$ 0,00</strong>
                </div>
                <button style="visibility: hidden;" id="btnRemoveItem" (click)="openModalRemoveItem()">teste</button>
            </div>
        </div>

        <div class="col-lg-7">
            <!-- categorias -->
            <div class="categorias">
                <button type="button" onclick="selecionaCategoria('0')" class="btn btn-categoria categoria-0">
                    Todos ({{ sizeof($produtos) }})
                </button>

                @foreach($categorias as $c)
                @if(sizeof($c->produtos) > 0)
                <button type="button" onclick="selecionaCategoria('{{ $c->id }}')" class="btn btn-categoria categoria-{{ $c->id }}">
                    {{ $c->nome }} ({{ sizeof($c->produtos) }})
                </button>
                @endif
                @endforeach

            </div>
            <div class="row">
                <div class="col-8 m-1">
                    <input id="inp-pesquisa" placeholder="Pesquisar produto [shift+f]" type="text" class="form-control">
                </div>
            </div>

            <div class="row produtos-lateral">
                @foreach($produtos as $p)
                @if($p->validaEstoque())
                <div class="col-6 col-lg-3 col-sm-3 col-xl-2 col-md-4 prod p-1 card-prod-{{ $p->id }}" onclick="adicionarItemCard('{{ $p->id }}')">
                    <div class="card">
                        @if($p->precoComPromocao())
                        <div class="ribbon" aria-hidden="true">Promoção</div>
                        @endif
                        <div class="card-header">
                            <img src="{{ $p->img }}">
                        </div>
                        <input type="hidden" class="categoria" value="{{ $p->categoria_id }}">
                        <div class="card-body d-flex flex-column justify-content-between" style="height: 70px;">
                            <h5 style="margin-top: -25px; font-size: 14px; margin-right: -20px; margin-left: -20px">{{ substr($p->nome, 0, 60) }} 
                                <strong class="text-muted" style="font-size: 14px">
                                    {{ strlen($p->referencia) > 0 && $p->referencia > 0 ? "REF:".$p->referencia : '' }}
                                </strong>
                                <strong class="text-danger" style="font-size: 14px">
                                    {{ strlen($p->referencia_balanca) > 0 && $p->referencia_balanca > 0 ? "BAL:".$p->referencia_balanca : '' }}
                                </strong>
                            </h5>

                            <h6 class="footer-text valor-produto text-primary" style="font-size: 15px">R$ {{ __moeda($p->valor_unitario) }}</h6>
                        </div>
                    </div>
                </div>
                @endif
                
                @endforeach
            </div>

            <div class="row btns">
                <div class="col-12 col-lg-6 col-xl-3 col-sm-6 p-0 div-footer mt-1">
                    <button type="button" class="btn btn-danger w-100" onclick="openModalCliente()">
                        <i class="ri-user-line"></i>
                        <span class="txt-cliente">
                            @if(isset($item) && $item->cliente_id)
                            {{ $item->cliente->info }}
                            @else
                            Cliente
                            @endif

                            @if(isset($clientePadrao) && $clientePadrao)
                            {{ $clientePadrao->info }}
                            @endif
                        </span>
                    </button>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 col-sm-6 p-0 div-footer mt-1">
                    <button type="button" class="btn btn-primary w-100" onclick="openModalVendedor()">
                        <i class="ri-user-2-fill"></i>
                        <span class="txt-vendedor">
                            @if(isset($item) && $item->funcionario)
                            {{ $item->funcionario->nome }}
                            @else
                            Vendedor
                            @endif
                        </span>
                    </button>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 col-sm-6 p-0 div-footer mt-1">
                    <button type="button" id="btnPagamentoMulti" class="btn btn-dark w-100" onclick="openModalMultiploPagamento()">
                        <i class="ri-price-tag-3-line"></i> Múltiplo pagamento <span class="atalhos">[F7]</span>
                    </button>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 col-sm-6 p-0 div-footer mt-1">
                    <button type="button" class="btn btn-warning w-100 text-white" onclick="openModalListaPreco()">
                        <i class="ri-file-list-line"></i> Lista de preços
                    </button>
                </div>
                <div class="col-12 col-xl-3 col-lg-6 p-0 mt-1 div-footer">
                    <button type="button" id="btnDinheiro" class="btn btn-success w-100 text-white" onclick="openModalDinheiro()">
                        <i class="ri-cash-line"></i> Dinheiro <span class="atalhos">[F4]</span>
                    </button>
                </div>
                <div class="col-12 col-xl-3 col-lg-6 p-0 mt-1 div-footer">
                    <button type="button" id="btnCartaoCredito" class="btn btn-dark w-100 text-white" onclick="openModalCartao('03')">
                        <i class="ri-bank-card-line"></i> Crédito <span class="atalhos">[F2]</span>
                    </button>
                </div>
                <div class="col-12 col-xl-3 col-lg-6 p-0 mt-1 div-footer">
                    <button type="button" id="btnCartaoDebito" class="btn btn-danger w-100 text-white"
                    onclick="openModalCartao('04')">
                    <i class="ri-bank-card-fill"></i> Débito <span class="atalhos">[F3]</span>
                </button>
            </div>
            <div class="col-12 col-xl-3 col-lg-6 p-0 mt-1 div-footer" onclick="setPagamento('17')">
                <button type="button" id="btnPix" class="btn btn-primary w-100 text-white">
                    <i class="ri-tablet-line"></i> PIX <span class="atalhos">[F9]</span>
                </button>
            </div>
        </div>
    </div>
</div>

<button type="button" id="btnVendasOffline" class="btn-vendas-offline">
    <i class="ri-error-warning-fill"></i>
    <span class="badge"></span>
</button>

</div>

<input type="hidden" value="{{ json_encode($produtos) }}" id="produtos-hidden">
<input type="hidden" value="{{ $config->balanca_digito_verificador }}" id="balanca_digito_verificador">
<input type="hidden" value="{{ $config->balanca_valor_peso }}" id="balanca_digito_verificador">
<input type="hidden" value="{{ $config->impressao_sem_janela_cupom }}" id="impressao_sem_janela_cupom">

<input type="hidden" value="{{ $isVendaSuspensa ? $item->id : 0 }}" id="venda_suspensa_id">
<input type="hidden" value="{{ isset($itensVenda) ? $item->id : 0 }}" id="venda_id">
<input type="hidden" value="{{ $isVendaSuspensa ? json_encode($itenSuspensa) : '[]' }}" id="itens_venda_suspensa">
<input type="hidden" value="{{ isset($itensVenda) ? json_encode($itensVenda) : '[]' }}" id="itens_venda">

<input type="hidden" id="definir_vendedor_pdv" value="{{ $config ? $config->definir_vendedor_pdv : 0 }}" name="">
<input type="hidden" id="cliente_padrao" value="{{ isset($clientePadrao) && $clientePadrao ? $clientePadrao->id : 0 }}" name="">

