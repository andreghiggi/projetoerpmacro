@section('css')
<link rel="stylesheet" type="text/css" href="/css/pdv.css">
@endsection

<input type="hidden" id="abertura" value="{{ $abertura }}" name="">
<input type="hidden" id="tef_hash" value="" name="tef_hash">
<input type="hidden" id="config_tef" value="{{ isset($configTef) && $configTef != null ? 1 : 0 }}">
<input type="hidden" id="agrupar_itens" value="{{ $config ? $config->agrupar_itens : 0 }}" name="">
<input type="hidden" id="definir_vendedor_pdv" value="{{ $config ? $config->definir_vendedor_pdv : 0 }}" name="">
<input type="hidden" id="venda_id" value="{{ isset($item) ? $item->id : '' }}">
<input type="hidden" id="lista_id" value="" name="lista_id">
<input type="hidden" id="alerta_sonoro" value="{{ $config ? $config->alerta_sonoro : 0 }}">
<input type="hidden" id="local_id" value="{{ $caixa->localizacao->id }}">
<input type="hidden" id="impressao_sem_janela_cupom" value="{{ $config ? $config->impressao_sem_janela_cupom : 0 }}">
<input type="hidden" id="documento_pdv" value="{{ $config ? $config->documento_pdv : 'nfce' }}">
<input type="hidden" id="NFECNPJ" value="{{ env('NFECNPJ') }}">

@if($isVendaSuspensa)
<input type="hidden" value="{{ $item->id }}" name="venda_suspensa_id">
@endif

@if(isset($isOrcamento) && $isOrcamento)
<input type="hidden" value="{{ $item->id }}" name="orcamento_id">
@endif

@isset($acrescimo)
<input type="hidden" value="{{ $acrescimo }}" id="acrescimo_pedido">
@endif

@isset($pedido)
@isset($isDelivery)
<input name="pedido_delivery_id" id="pedido_delivery_id" value="{{ $pedido->id }}" class="d-none">
<input id="pedido_desconto" value="{{ $pedido->desconto ? $pedido->desconto : 0 }}" class="d-none">
<input name="valor_entrega" id="pedido_valor_entrega" value="{{ $pedido->valor_entrega }}" class="d-none">
@else
<input name="pedido_id" id="pedido_id" value="{{ $pedido->id }}" class="d-none">
@isset($pushItensPedido)
<input name="itens_cliente" id="pedido_id" value="{{ json_encode($pushItensPedido) }}" class="d-none">
@endif
@endif
@endif

@isset($agendamento)
<input name="agendamento_id" value="{{ $agendamento->id }}" class="d-none">
@endif

<input type="hidden" id="inp-finalizacao_pdv" value="{{ __finalizacaoPdv() }}">

<input type="hidden" id="estoque_view" value="@can('estoque_view') 1 @else 0 @endif">

<div class="row">
    <div class="col-lg-4">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="card widget-icon-box">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-muted text-uppercase fs-13 mt-0">Cliente</h5>
                                @isset($cliente)
                                <h6 class="cliente_selecionado">{{ $cliente->razao_social }}</h6>
                                @else
                                <h6 class="cliente_selecionado">--</h6>
                                @endif
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <button type="button" class="avatar-title text-bg-success rounded rounded-3 fs-3 widget-icon-box-avatar shadow btn-selecionar_cliente" data-bs-toggle="modal" data-bs-target="#cliente">
                                    <i class="ri-group-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="card widget-icon-box">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-muted text-uppercase fs-13 mt-0" title="Conversation Ration">Vendedor</h5>
                                @isset($funcionario)
                                <h6 class="funcionario_selecionado">{{ $funcionario->nome }}</h6>
                                @else
                                <h6 class="funcionario_selecionado">--</h6>
                                @endif
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <button type="button" class="avatar-title text-bg-warning rounded rounded-3 fs-3 widget-icon-box-avatar" data-bs-toggle="modal" data-bs-target="#funcionario">
                                    <i class=" ri-user-2-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="height: 757px">

            <hr>
            <h5 class="text-center">Categorias</h5>
            <div class="card categorias m-1" data-simplebar data-simplebar-lg style="height: 60px;">
                <div class="d-flex g m-1">

                    <button type="button" id="cat_todos" onclick="todos()" class="btn btn-cat">Todos</button>
                    @foreach ($categorias as $cat)
                    <button type="button" class="btn btn_cat_{{ $cat->id }} btn-cat" onclick="selectCat('{{ $cat->id }}')">{{$cat->nome}}</button>
                    @endforeach
                </div>
            </div>
            <h4 class="text-center mt-3">Produtos</h4>
            <div class="card-body lista_produtos m-1" data-simplebar data-simplebar-lg style="max-height: 532px;">
                <div class="row cards-categorias">
                </div>
            </div>
            <div class="row" style="margin-top: 0px">
                <div class="col-1 text-center">
                    <input class="mousetrap" type="" autofocus style="border: none; width: 10px; height: 10px; background-color:black" id="codBarras">
                </div>
                <div class="col-6 leitor_ativado text-info">
                    Leitor Ativado
                </div>
                <div class="col-6 leitor_desativado d-none">
                    Leitor Desativado
                </div>
                @if(__countLocalAtivo() > 1 && $caixa->localizacao)
                <div class="col-5 text-end">
                    <strong class="text-danger" style="margin-right: 5px;">{{ $caixa->localizacao->descricao }}</strong>
                </div>
                @endif

            </div>

        </div>
    </div>
    <div class="col-lg-8 produtos">
        <div class="card" style="height: 850px">
            <div class="row m-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="inp-produto_id" class="">Produto</label>
                        <div class="input-group">
                            <select class="form-control produto_id" name="produto_id" id="inp-produto_id"></select>
                        </div>
                        <input name="variacao_id" id="inp-variacao_id" type="hidden" value="">

                    </div>
                </div>
                <div class="col-md-2">
                    {!! Form::tel('quantidade', 'Quantidade')->attrs(['class' => 'qtd']) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::tel('valor_unitario', 'Valor Unitário')->attrs(['class' => 'moeda value_unit']) !!}
                </div>
                <div class="col-md-2">
                    <div class="row">
                        <div class="col-12">
                            <br>
                            <button class="btn btn-primary btn-add-item w-100" type="button" style="margin-left: 0px">Adicionar</button>
                        </div>

                    </div>
                </div>
                <div class="col-md-1">
                    {!! Form::hidden('subtotal', 'SubTotal')->attrs(['class' => 'moeda']) !!}
                    {!! Form::hidden('valor_total', 'valor Total')->attrs(['class' => 'moeda']) !!}
                </div>
            </div>
            <div class="card m-1">
                <div data-bs-target="#navbar-example2" class="scrollspy-example" style="height: 440px">
                    <table class="table table-striped dt-responsive nowrap table-itens">
                        <thead class="table-dark">
                            <tr>
                                <th></th>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Valor</th>
                                <th>Subtotal</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($item))
                            @foreach ($item->itens as $key => $product)
                            <tr class="line-product">
                                <input readonly type="hidden" name="key" class="form-control" value="{{ $product->key }}">
                                <input readonly type="hidden" name="produto_id[]" class="produto_row" value="{{ $product->produto->id }}">
                                <input name="variacao_id[]" type="hidden" value="{{ $product->variacao_id }}">

                                <td>
                                    <img src="{{ $product->produto->img }}" style="width: 30px; height: 40px; border-radius: 10px;">
                                </td>
                                <td>
                                    <input style="width: 350px" readonly type="text" name="produto_nome[]" class="form-control" value="{{ $product->produto->nome }} @if($product->produtoVariacao != null) - {{ $product->produtoVariacao->descricao }} @endif">
                                </td>

                                <td class="datatable-cell">
                                    <div class="form-group mb-2" style="width: 200px">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <button id="btn-subtrai" class="btn btn-danger" type="button">-</button>
                                            </div>
                                            <input type="tel" readonly class="form-control qtd qtd_row" name="quantidade[]" value="{{ number_format($product->quantidade, 2, ',', '') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-success" id="btn-incrementa" type="button">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 100px" readonly type="tel" name="valor_unitario[]" class="form-control value-unit" value="{{ __moeda($product->valor_unitario) }}">
                                </td>
                                <td>
                                    <input style="width: 100px" readonly type="tel" name="subtotal_item[]" class="form-control subtotal-item" value="{{ __moeda($product->valor_unitario * $product->quantidade) }}">
                                </td>
                                <td>

                                    @php
                                    $adds = '';

                                    if($product && $product->adicionais){
                                        foreach($product->adicionais as $a){
                                            $adds .= "$a->id,";
                                        }
                                    }
                                    @endphp
                                    <div class="inputs-adicional">
                                        @if($product->adicionais)
                                        @foreach($product->adicionais as $a)
                                        <input class='add' type='hidden' value='{{ $a->adicional_id }}' />
                                        @endforeach
                                        @endif
                                    </div>
                                    <input type="hidden" value="{{ $adds }}" class="adicionais" name="adicionais[]">
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-row"><i class="ri-delete-bin-line"></i></button>
                                </td>

                            </tr>
                            @endforeach
                            @endif

                            @if (isset($servicos))
                            @foreach ($servicos as $key => $servico)
                            <tr>
                                <input readonly type="hidden" name="servico_id[]" class="form-control" value="{{ $servico->servico->id }}">

                                <td>
                                    <img src="{{ $servico->servico->img }}" style="width: 30px; height: 40px; border-radius: 10px;">
                                </td>
                                <td style="width: 350px">
                                    <input readonly type="text" name="servico_nome[]" class="form-control" value="{{ $servico->servico->nome }} [serviço]" style="color: darkred;">
                                </td>
                                <td>
                                    <div class="input-group" style="width: 200px">
                                        <div class="input-group-prepend">
                                            <button disabled id="btn-subtrai" class="btn btn-danger" type="button">-</button>
                                        </div>
                                        <input readonly type="tel" name="quantidade_servico[]" class="form-control qtd-item" value="{{ number_format($servico->quantidade,0) }}">
                                        <div class="input-group-append">
                                            <button disabled class="btn btn-success" id="btn-incrementa" type="button">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input readonly type="tel" style="width: 100px" name="valor_unitario_servico[]" class="form-control" value="{{ __moeda($servico->valor) }}">
                                </td>
                                <td>
                                    <input readonly type="tel" style="width: 100px" name="subtotal_servico[]" class="form-control subtotal-item" value="{{ __moeda($servico->valor * $servico->quantidade) }}">
                                </td>
                                <td>
                                    <button disabled type="button" class="btn btn-danger btn-sm btn-delete-row"><i class="ri-delete-bin-line"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            @endif

                            @if (isset($pedido) && isset($itens))
                            @foreach ($itens as $key => $product)
                            <tr class="line-product">
                                <input readonly type="hidden" name="key" class="form-control" value="{{ $product->key }}">
                                <input readonly type="hidden" name="produto_id[]" class="produto_row" value="{{ $product->produto->id }}">
                                <input name="variacao_id[]" type="hidden" value="{{ $product->variacao_id }}">

                                <td>
                                    <img src="{{ $product->produto->img }}" style="width: 30px; height: 40px; border-radius: 10px;">
                                </td>
                                <td>
                                    <input style="width: 350px" readonly type="text" name="produto_nome[]" class="form-control" value="{{ $product->produto->nome }} @if($product->produtoVariacao != null) - {{ $product->produtoVariacao->descricao }} @endif">
                                </td>

                                <td class="datatable-cell">
                                    <div class="form-group mb-2" style="width: 200px">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <button id="btn-subtrai" class="btn btn-danger" type="button">-</button>
                                            </div>
                                            <input type="tel" readonly class="form-control qtd qtd_row" name="quantidade[]" value="{{ number_format($product->quantidade, 2, ',', '') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-success" id="btn-incrementa" type="button">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 100px" readonly type="tel" name="valor_unitario[]" class="form-control value-unit" value="{{ __moeda($product->valor_unitario) }}">
                                </td>
                                <td>
                                    <input style="width: 100px" readonly type="tel" name="subtotal_item[]" class="form-control subtotal-item" value="{{ __moeda($product->valor_unitario * $product->quantidade) }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-row"><i class="ri-delete-bin-line"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="">

            <div class="row">
                <div class="col-md-6">
                    <h5 style="margin-left: 10px">Total de itens: <strong class="total-itens text-danger">0</strong></h5>
                </div>
                <div class="col-md-6 text-end">
                    <h5 style="margin-right: 10px">Total de linhas: <strong class="total-linhas text-danger">0</strong></h5>
                </div>
                <!-- <div class="col-md-7">
                    <h4 class="">Finalização da Venda</h4>
                </div> -->
            </div>
            <div class="row m-1">
                <div class="col-lg-3 col-md-4">
                    <div class="card widget-icon-box ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-muted text-uppercase fs-13 mt-0" title="Number of Customers">Desconto</h5>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <button type="button" onclick="setaDesconto()" class="avatar-title text-bg-primary rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                        <i class="ri-checkbox-indeterminate-line"></i>
                                    </button>
                                </div>
                            </div>
                            <h3 id="valor_desconto">R$ {{ isset($item) ? __moeda($item->desconto) : '0,00' }}</h3>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <div class="col-lg-3 col-md-4">
                    <div class="card widget-icon-box ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-muted text-uppercase fs-13 mt-0" title="Number of Customers">Acréscimo</h5>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <button type="button" onclick="setaAcrescimo()" class="avatar-title text-bg-warning rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                        <i class="ri-add-box-line"></i>
                                    </button>
                                </div>
                            </div>
                            <h3 id="valor_acrescimo">R$ {{ isset($item) ? __moeda($item->acrescimo) : '0,00' }}</h3>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->

                <div class="col-lg-3 col-md-4">
                    <div class="card widget-icon-box ">
                        <div class="card-body">
                            <div class="row mt-1">
                                <div class="col-6">
                                    <div class="row">
                                        <h5 class="text-center">SUPRIMENTO</h5>
                                    </div>
                                    <div class="avatar-sm m-1 mt-2">
                                        <button type="button" style="margin-left: 35px" data-bs-toggle="modal" data-bs-target="#suprimento_caixa" class="avatar-title text-bg-info rounded rounded-3 fs-3 widget-icon-box-avatar">
                                            <i class="ri-add-box-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <h5 class="text-center">SANGRIA</h5>
                                    </div>
                                    <div class="avatar-sm m-1 mt-2">
                                        <button type="button" style="margin-left: 35px" data-bs-toggle="modal" data-bs-target="#sangria_caixa" class="avatar-title text-bg-danger rounded rounded-3 fs-3 widget-icon-box-avatar">
                                            <i class="ri-checkbox-indeterminate-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->

                <div class="col-lg-3 col-md-4">
                    <div class="card widget-icon-box ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-muted text-uppercase fs-13 mt-0" title="Number of Customers">TOTAL</h5>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title text-bg-dark rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                        <i class="ri-shopping-cart-fill"></i>
                                    </span>
                                </div>
                            </div>
                            <h3 class="">
                                @isset($item)
                                <strong class="total-venda">{{ __moeda($item->valor_total) }}</strong>
                                @else
                                <strong class="total-venda">0,00</strong>
                                @endif
                            </h3>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->

                <div class="col-lg-3 col-md-8">
                    <div class="card widget-icon-box ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-muted text-uppercase fs-13 mt-0" title="Number of Orders">Tipo de Pagamento</h5>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title text-bg-success rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                        <i class=" ri-money-dollar-circle-line"></i>
                                    </span>
                                </div>
                            </div>

                            {!! Form::select('tipo_pagamento', '', ['' => 'Selecione'] + $tiposPagamento)->attrs(['class' => 'form-select tp-pag'])->value(isset($item) ? $item->tipo_pagamento : '') !!}

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <div class="col-lg-5 col-md-6 div-troco d-none">
                    <div class="card " style="height: 140px">
                        <div class="row m-2">
                            <div class="col-lg-5 mt-4">
                                <h5>Valor Recebido</h5>
                            </div>
                            <div class="col-lg-7">
                                {!! Form::tel('valor_recebido', '')->attrs(['class' => 'moeda']) !!}
                            </div>
                        </div>
                        <div class="row m-1">
                            <div class="card text-bg-danger">
                                <h3 class="m-1">TROCO = <strong class="" id="valor-troco"></strong></h3>
                                <input type="hidden" name="troco" id="inp-troco">
                            </div>
                        </div>
                    </div> <!-- end card-->
                </div> <!-- end col-->
                
                <!-- <div class="col-lg-2 col-6 div-vencimento d-none">
                    <div class="card ">
                        <div class="row m-2">
                            <div class="text-center">
                                <h5>Data de vencimento</h5>
                            </div>
                            <div>
                                {!! Form::date('data_vencimento', '')->attrs(['class' => 'data_atual']) !!}
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="col-xl-5 col-md-6 div-btns">
                    <div class="card widget-icon-box" style="height: 140px">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-xl-6 col-12">
                                    <button type="button" class="btn btn-sm btn-warning w-100 btn-pagamento-multi mt-1" data-bs-toggle="modal" data-bs-target="#pagamento_multiplo"><i class="ri-list-check-3"></i> Pagamento múltiplo</button>
                                </div>

                                <div class="col-md-12 col-xl-6 col-12">
                                    <button type="button" class="btn btn-sm btn-dark w-100 mt-1" data-bs-toggle="modal" data-bs-target="#lista_precos"><i class="ri-cash-line"></i> Lista de preços</button>
                                </div>
                                <div class="col-md-12 col-xl-6 col-12">
                                    <button type="button" class="btn btn-sm btn-primary w-100 mt-1" data-bs-toggle="modal" data-bs-target="#observacao_pdv"><i class="ri-file-edit-fill"></i> Observação</button>
                                </div>
                                @if(!isset($item))
                                <div class="col-md-12 col-xl-6 col-12">
                                    <button type="button" class="btn btn-sm btn-light w-100 btn-vendas-suspensas mt-1" data-bs-toggle="modal" data-bs-target="#vendas_suspensas"><i class="ri-time-fill"></i> Vendas suspensas</button>
                                </div>
                                @endif

                                <div class="col-md-12 col-xl-6 col-12">
                                    <button type="button" class="btn btn-sm btn-light w-100 mt-1" onclick="modalFrete()"><i class="ri-truck-line"></i> Frete <strong class="valor-frete">R$ {{ isset($item) ? __moeda($item->valor_frete) : '0,00' }}</strong></button>
                                </div>

                                @if(!isset($item))
                                <div class="col-md-12 col-xl-6 col-12">
                                    <button type="button" class="btn btn-sm btn-secondary w-100 btn-orcamentos mt-1" data-bs-toggle="modal" data-bs-target="#orcamentos"><i class="ri-list-settings-fill"></i> Orçamentos</button>
                                </div>
                                @endif

                                <div class="col-md-12 col-xl-6 col-12">
                                    <button type="button" class="btn btn-sm btn-dark w-100 mt-1 btn-fatura-padrao d-none">
                                        <i class="ri-booklet-line"></i>
                                        Fatura Padrão do Cliente
                                    </button>
                                </div>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <div class="col-md-6 col-xl-4">
                    <div class="card widget-icon-box " style="height: 140px">
                        <div class="card-body">
                            <div class="row">

                                <a class="btn btn-danger btn-sm w-50 mt-2" href="{{ route('frontbox.index')}}" style="margin-top: -20px">
                                    <i class="ri-arrow-left-s-line"></i>
                                    Sair do PDV
                                </a>

                                @if($isVendaSuspensa == 0 && $isOrcamento == 0)
                                <button type="button" id="btn-suspender" class="btn btn-light btn-sm w-50 mt-2" style="margin-top: -20px">
                                    <i class="ri-timer-line"></i>
                                    Suspender Venda
                                </button>
                                @else
                                <a href="{{ route('frontbox.create') }}" class="btn btn-light btn-sm w-50 mt-2" style="margin-top: -20px">
                                    <i class="ri-refresh-line"></i>
                                    Nova Venda
                                </a>
                                @endif

                                @if(isset($item) && $isVendaSuspensa == 0 && $isOrcamento == 0)
                                <button type="button" class="btn btn-success w-100 mt-4" disabled id="editar_venda">
                                    <i class="ri-checkbox-circle-line"></i>
                                    Editar venda
                                </button>
                                @else
                                <button type="button" class="btn btn-success w-100 mt-4" disabled id="salvar_venda">
                                    <i class="ri-checkbox-circle-line"></i>
                                    Finalizar venda
                                </button>
                                @endif
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->

                @if($isVendaSuspensa)
                <button id="btnSuspenderVenda" class="btn-bolinha-flutuante">
                    <i class="ri-pause-circle-fill"></i>
                </button>
                @endif

            </div>
        </div>
    </div>
</div>



</div>

@include('modals._pagamento_multiplo', ['not_submit' => true])
@include('modals._finalizar_venda', ['not_submit' => true])
@include('modals._funcionario', ['not_submit' => true])
@include('modals._cartao_credito', ['not_submit' => true])
@include('modals._variacao', ['not_submit' => true])
@include('modals._lista_precos')
@include('modals._vendas_suspensas')
@include('front_box.partials._modal_orcamentos')
@include('modals._tef_consulta')
@include('modals._valor_credito')
@include('modals._modal_pix')
@include('modals._fatura_venda')
@include('modals._frete')

@include('modals._observacao_pdv')
@include('modals._adicionais_pdv')
@include('modals._cliente', ['cashback' => 1])

@section('js')
<script>
    var senhaAcao = "";

    @if(isset($config) && strlen(trim($config->senha_manipula_valor)) > 1)
    senhaAcao = "{{ $config->senha_manipula_valor }}";
    @endif
</script>
<script src="/js/frente_caixa.js" type=""></script>
<script src="/js/comanda_pdv.js"></script>

<script type="text/javascript" src="/js/mousetrap.js"></script>
<script type="text/javascript" src="/js/controla_conta_empresa.js"></script>
<script src="/js/novo_cliente.js"></script>

<script type="text/javascript">

    @if(Session::has('sangria_id'))
    window.open(path_url + 'sangria-print/' + {{ Session::get('sangria_id') }}, "_blank")
    @endif
    @if(Session::has('suprimento_id'))
    window.open(path_url + 'suprimento-print/' + {{ Session::get('suprimento_id') }}, "_blank")
    @endif

    $('.btn-novo-cliente').click(() => {
        $('.modal-select-cliente .btn-close').trigger('click')
        $('#modal_novo_cliente').modal('show')

    })
</script>

@endsection
