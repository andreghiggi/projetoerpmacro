@section('css')
<style>
    .active {
        background: rgb(85, 114, 245) !important;
        color: #fff !important;
    }

    #salvar_venda:hover {
        cursor: pointer;
    }

    .btn-cat{
        height: 30px;
        display: block;
        min-width: 200px;
    }

</style>

<link rel="stylesheet" type="text/css" href="/css/pdv.css">
@endsection

<input type="hidden" id="abertura" value="{{ $abertura }}" name="">
<input type="hidden" id="valor_total_old" value="{{ $item->total }}">
<input type="hidden" id="venda_id" value="{{ isset($item) ? $item->id : '' }}">
<input type="hidden" id="tipo" value="{{ isset($item->troco) ? 'nfce' : 'nfe' }}">
<input type="hidden" id="lista_id" value="" name="lista_id">
<input type="hidden" id="local_id" value="{{ $caixa->localizacao->id }}">

@if($isVendaSuspensa)
<input type="hidden" value="{{ $item->id }}" name="venda_suspensa_id">
@endif

@isset($pedido)
@isset($isDelivery)
<input name="pedido_delivery_id" id="pedido_delivery_id" value="{{ $pedido->id }}" class="d-none">
<input id="pedido_desconto" value="{{ $pedido->desconto }}" class="d-none">
<input id="pedido_valor_entrega" value="{{ $pedido->valor_entrega }}" class="d-none">
@else
<input name="pedido_id" id="pedido_id" value="{{ $pedido->id }}" class="d-none">
@endif
@endif

@if(isset($config))
<input type="hidden" id="inp-abrir_modal_cartao" value="{{ $config != null ? $config->abrir_modal_cartao : 0 }}">
<input type="hidden" id="inp-senha_manipula_valor" value="{{ $config != null ? $config->senha_manipula_valor : '' }}">
@else
<input type="hidden" id="inp-abrir_modal_cartao" value="0">
<input type="hidden" id="inp-senha_manipula_valor" value="">
@endif

@isset($agendamento)
<input name="agendamento_id" value="{{ $agendamento->id }}" class="d-none">
@endif

<input type="hidden" id="estoque_view" value="@can('estoque_view') 1 @else 0 @endif">

<div class="row">
    <div class="col-lg-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card widget-icon-box">
                    <div class="card-body" style="height: 89px;">

                        @isset($cliente)
                        <h5 class="cliente_selecionado">Cliente: 
                            <strong class="text-primary">{{ $cliente->razao_social }}</strong>
                        </h5>

                        @else
                        <div class="row">
                            <div class="col-md-9">
                                <h5 class="cliente_selecionado">
                                    Cliente: <strong class="text-primary">Consumidor final</strong> 
                                </h5>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#cliente" type="button"><i class="ri-pencil-fill"></i></button>
                            </div>
                        </div>
                        @endif


                        @isset($funcionario)
                        <h5 class="funcionario_selecionado">Vendedor: <strong class="text-primary">{{ $funcionario->nome }}</strong></h5>
                        @else
                        <h5 class="funcionario_selecionado">Vendedor: <strong class="text-primary">--</strong></h5>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
        <div class="card" style="height: 750px">

            <hr>
            <h5 class="text-center">Categorias</h5>
            <div class="card categorias m-1" data-simplebar data-simplebar-lg style="height: 60px;">
                <div class="d-flex g m-2">
                    <button type="button" id="cat_todos" onclick="todos()" class="btn btn-cat">Todos</button>
                    @foreach ($categorias as $cat)
                    <button type="button" class="btn btn_cat_{{ $cat->id }} btn-cat" onclick="selectCat('{{ $cat->id }}')">{{$cat->nome}}</button>
                    @endforeach
                </div>
            </div>
            <h4 class="text-center mt-3">Produtos</h4>
            <div class="card-body lista_produtos m-1" data-simplebar data-simplebar-lg style="max-height: 522px;">
                <div class="row cards-categorias">

                </div>
            </div>
            <div class="row" style="margin-top: 0px">
                <div class="col-1 text-center">
                    <input class="mousetrap" type="" autofocus style="border: none; width: 10px; height: 10px; background-color:black" id="codBarras" name="">
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
                    {!! Form::tel('quantidade', 'Quantidade')->attrs(['data-mask' => '00000,000', 'data-mask-reverse' => "true"]) !!}
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
                    {!! Form::hidden('subtotal', '')->attrs(['class' => 'moeda']) !!}
                    {!! Form::hidden('valor_total', '')->attrs(['class' => 'moeda']) !!}
                    {!! Form::hidden('valor_pagar', '')->attrs(['class' => '']) !!}
                    {!! Form::hidden('valor_credito', '')->attrs(['class' => '']) !!}
                </div>
            </div>
            <div class="card m-1">
                <div data-bs-target="#navbar-example2" class="scrollspy-example" style="height: 440px">
                    <table class="table table-striped dt-responsive nowrap table-itens">
                        <thead>
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
                                                <button id="btn-subtrai" class="btn btn-danger btn-qtd" type="button">-</button>
                                            </div>
                                            <input type="tel" readonly class="form-control qtd qtd_row" name="quantidade[]" value="{{ number_format($product->quantidade, 2, ',', '') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-success btn-qtd" id="btn-incrementa" type="button">+</button>
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

                            @if (isset($servicos))
                            @foreach ($servicos as $key => $servico)
                            <tr>
                                <input readonly type="hidden" name="servico_id[]" class="form-control" value="{{ $servico->servico->id }}">

                                <td>
                                    <img src="{{ $servico->servico->img }}" style="width: 30px; height: 40px; border-radius: 10px;">
                                </td>
                                <td style="width: 500px">
                                    <input readonly type="text" name="servico_nome[]" class="form-control" value="{{ $servico->servico->nome }} [serviço]" style="color: darkred;">
                                </td>
                                <td>
                                    <div class="input-group">
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
                                    <input readonly type="tel" name="valor_unitario_servico[]" class="form-control" value="{{ __moeda($servico->valor) }}">
                                </td>
                                <td>
                                    <input readonly type="tel" name="subtotal_servico[]" class="form-control subtotal-item" value="{{ __moeda($servico->valor * $servico->quantidade) }}">
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
            <h4 class="text-center">Finalização da Troca</h4>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="card widget-icon-box div-pagamento">
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
                            <h3 id="valor_desconto">R$ 0,00</h3>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <div class="col-lg-3 col-6">
                    <div class="card widget-icon-box div-pagamento">
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
                            <h3 id="valor_acrescimo">R$ 0,00</h3>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->

                <div class="col-lg-3 col-6">
                    <div class="card widget-icon-box div-pagamento">
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

                <div class="col-lg-3 col-6">
                    <div class="card widget-icon-box div-pagamento">
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
            </div>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="card widget-icon-box div-pagamento" style="height: 93%">
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

                            {!! Form::select('tipo_pagamento', '', ['' => 'Selecione'] + $tiposPagamento)->attrs(['class' => 'form-select'])->value(isset($item) ? $item->tipo_pagamento : '') !!}

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <div class="col-lg-3 col-6 div-troco d-none">
                    <div class="card div-pagamento" style="height: 93%">
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
                <div class="col-lg-2 col-6 div-vencimento d-none">
                    <div class="card div-pagamento" style="height: 93%">
                        <div class="row m-2">
                            <div class="text-center">
                                <h5>Data dde vencimento</h5>
                            </div>
                            <div>
                                {!! Form::date('data_vencimento', '')->attrs(['class' => 'data_atual']) !!}
                            </div>
                        </div>
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <div class="col">
                    <div class="card widget-icon-box div-pagamento" style="height: 93%">
                        <div class="card-body">
                            <div class="">
                                <h4 class="text-muted">Valor original da venda: <strong class="text-primary">R$ {{ __moeda($item->total) }}</strong></h4>
                                <h4 class="h-valor_pagar text-muted">Valor à pagar: <strong class="text-success valor_pagar">R$ {{ __moeda(0) }}</strong></h4>
                                <h4 class="h-valor_restante d-none">Valor restante: <strong class="text-warning valor_restante">R$ {{ __moeda(0) }}</strong></h4>
                                <h5>Data da venda: <strong class="text-danger">{{ __data_pt($item->created_at) }}</strong></h5>
                                <!-- <h4>VALOR DE DIFERENÇA: <strong class="text-danger valor_diferenca">R$ {{ __moeda(0) }}</strong></h4> -->
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <div class="col">
                    <div class="card widget-icon-box div-pagamento" style="height: 93%">
                        <div class="card-body">
                            <div class="row">

                                <a class="btn btn-danger btn-sm w-50 mt-2" href="{{ route('trocas.index')}}" style="margin-top: -20px">
                                    <i class="ri-arrow-left-s-line"></i>
                                    Sair da Troca
                                </a>


                                <button type="button" class="btn btn-success w-100 mt-4 salvar_troca" disabled id="salvar_venda" data-bs-toggle="modal" data-bs-target="#finalizar_troca">
                                    <i class="ri-checkbox-line"></i>
                                    Finalizar Troca
                                </button>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
            </div>
            {{-- <div class="row">
                <div class="col-sm-6 col-lg-3">
                    {!! Form::select('forma_pagamento', 'Forma de Pagamento')->attrs(['class' => 'form-select']) !!}
                </div>
                <div class="col-sm-6 col-lg-3">
                    {!! Form::select('tipo_pagamento', 'Tipo de Pagamento')->attrs(['class' => 'form-select']) !!}
                </div>
            </div> --}}

        </div>
    </div>
</div>
</div>

@include('modals._pagamento_multiplo', ['not_submit' => true])
@include('modals._finalizar_troca', ['not_submit' => true])
@include('modals._funcionario', ['not_submit' => true])
@include('modals._cartao_credito', ['not_submit' => true])
@include('modals._variacao', ['not_submit' => true])
@include('modals._lista_precos')
@include('modals._vendas_suspensas')
@include('modals._cliente', ['cashback' => 1])

@section('js')
<script>
    var senhaAcao = "";

    @if(isset($config) && strlen(trim($config->senha_manipula_valor)) > 1)
    senhaAcao = "{{ $config->senha_manipula_valor }}";
    @endif
</script>
@if($msgTroca != "")
<script type="text/javascript">
    toastr.warning('{{ $msgTroca }}');
</script>
@endif
<script src="/js/frente_caixa.js" type=""></script>
<script type="text/javascript" src="/js/mousetrap.js"></script>
<script type="text/javascript" src="/js/controla_conta_empresa.js"></script>
<script type="text/javascript" src="/js/controla_troca.js"></script>
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
