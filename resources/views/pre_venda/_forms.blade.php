@section('css')
<style>
    .active {
        background: rgb(85, 114, 245) !important;
        color: #fff !important;
    }

    #salvar_venda:hover {
        cursor: pointer;
    }

    .info-pre-venda{
        padding: 3px;
        border-radius: 3px;
        margin-right: 5px;
        text-align: center;
    }

    .btn-cat{
        height: 30px;
        display: block;
        min-width: 200px;
    }
</style>
@endsection

<input type="hidden" id="lista_id" value="" name="lista_id">

<input type="hidden" id="abertura" value="{{ $abertura }}" name="">
<input type="hidden" id="local_id" value="{{ $caixa->localizacao->id }}">

@isset($pedido)
<input name="pedido_id" id="pedido_id" value="{{ $pedido->id }}" class="d-none">
@endif
<div class="row ">
    <div class="col-lg-4">
        <div class="row">
            <div class="col-lg-6">
                <div class="card widget-icon-box">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-muted text-uppercase fs-13 mt-0" title="Number of Customers">Cliente</h5>
                                <h4 class="cliente_selecionado">
                                    {{ (isset($item) && $item->cliente) ? $item->cliente->razao_social : '--'}}
                                </h4>
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
            <div class="col-lg-6">
                <div class="card widget-icon-box">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-muted text-uppercase fs-13 mt-0" title="Conversation Ration">Vendedor</h5>
                                <h4 class="funcionario_selecionado">
                                    {{ (isset($item) && $item->funcionario) ? $item->funcionario->nome : '--'}}
                                </h4>
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
        <div class="card" style="height: 750px">
            <hr>
            <h5 class="text-center">Categorias</h5>
            @if(__countLocalAtivo() > 1 && $caixa->localizacao)
            <strong class="text-danger" style="position: absolute; left: 10px;">{{ $caixa->localizacao->descricao }}</strong>
            @endif
            <div class="card categorias m-1" data-simplebar data-simplebar-lg style="height: 60px;">
                <div class="d-flex g m-2">
                    <button type="button" id="cat_todos" onclick="todos()" class="btn btn-cat">Todos</button>
                    @foreach ($categorias as $cat)
                    <button type="button" class="btn btn_cat_{{ $cat->id }} btn-cat" onclick="selectCat('{{ $cat->id }}')">{{$cat->nome}}</button>
                    @endforeach
                </div>
            </div>


            <!-- <div class="col-11" style="margin-left: 24px">

            </div>
            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-end">Categorias</h5>
                </div>
                <div class="col-md-6 text-end">
                    @if(__countLocalAtivo() > 1 && $caixa->localizacao)
                    <strong class="text-danger" style="margin-right: 5px;">{{ $caixa->localizacao->descricao }}</strong>
                    @endif
                </div>
            </div> -->
            <!-- <div class="card categorias m-1" data-simplebar data-simplebar-lg style="max-height: 110px;">
                <div class="d-flex gap-1 m-2">
                    <button type="button" id="cat_todos" onclick="todos()" class="btn btn-light rounded-pill m-1">Todos</button>
                    @foreach ($categorias as $cat)
                    <button type="button" class="btn btn-light rounded-pill m-1 btn_cat btn_cat_{{ $cat->id }}" onclick="selectCat('{{ $cat->id }}')">{{$cat->nome}}</button>
                    @endforeach
                </div>
            </div> -->
            <h4 class="text-center mt-3">Produtos</h4>
            <div class="card-body lista_produtos m-1" data-simplebar data-simplebar-lg style="max-height: 522px;">
                <div class="row cards-categorias">

                </div>
            </div>
            <div class="row">
                <div class="col-1 text-center">
                    <input class="mousetrap" type="" autofocus style="border: none; width: 10px; height: 10px; background-color:black" id="codBarras" name="">
                </div>
                <div class="col-5 col-lg-2 leitor_ativado">
                    Leitor Ativado
                </div>
                <div class="col-5 col-lg-2 leitor_desativado d-none">
                    Leitor Desativado
                </div>

                <div class="col-6 col-lg-9">
                    <a href="{{ route('pre-venda.create') }}" class="float-end text-bg-primary info-pre-venda w-100">
                        NOVA PRÉ VENDA
                    </a>
                </div>
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
                        <div class="col-11">
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
                <div data-bs-target="#navbar-example2" class="scrollspy-example" style="height: 480px">
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
                                <input readonly type="hidden" name="produto_id[]" class="form-control" value="{{ $product->produto->id }}">
                                <td>
                                    <img src="{{ $product->produto->img }}" style="width: 30px; height: 40px; border-radius: 10px;">
                                </td>
                                <td>
                                    <input style="width: 350px" readonly type="text" name="produto_nome[]" class="form-control" value="{{ $product->produto->nome }}">
                                </td>
                                <td>
                                    <div class="form-group mb-2" style="width: 200px">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <button id="btn-subtrai" class="btn btn-danger" type="button">-</button>
                                            </div>
                                            <input readonly type="tel" name="quantidade[]" class="form-control qtd qtd-item" value="{{ number_format($product->quantidade,0) }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-success" id="btn-incrementa" type="button">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input style="width: 100px" readonly type="tel" name="valor_unitario[]" class="form-control value-unit" value="{{ __moeda($product->valor) }}">
                                </td>
                                <td>
                                    <input style="width: 100px" readonly type="tel" name="subtotal_item[]" class="form-control subtotal-item" value="{{ __moeda($product->quantidade * $product->valor) }}">
                                </td>
                                <td>
                                    <input type="hidden" class="adicionais" name="adicionais[]">
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-row"><i class="ri-delete-bin-line"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="">

                <div class="row">
                    <div class="col">
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

                                <h3 id="valor_desconto">R$ {{ isset($item) ? __moeda($item->desconto) : '0,00'}}</h3>
                                <input type="hidden" name="desconto" id="inp-valor_desconto">
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                    <div class="col">
                        <div class="card widget-icon-box div-pagamento">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="text-muted text-uppercase fs-13 mt-0" title="Number of Customers">Acréscimo</h5>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <button type="button" onclick="setaAcrescimo()" class="avatar-title text-bg-danger rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                            <i class="ri-add-box-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <h3 id="valor_acrescimo">R$ {{ isset($item) ? __moeda($item->acrescimo) : '0,00'}}</h3>
                                <input type="hidden" name="acrescimo" id="inp-valor_acrescimo">
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col">
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
                                {!! Form::select('tipo_pagamento', '', ['' => 'Selecione'] + App\Models\Nfce::tiposPagamento())->attrs(['class' => 'form-select'])->value(isset($item) ? $item->tipo_pagamento : '') !!}
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                    <div class="col div-vencimento d-none">
                        <div class="card div-pagamento" style="height: 93%">
                            <div class="row m-2">
                                <div class="text-center">
                                    <h4>Data Vencimento</h4>
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
                                <div class="d- mt-3">
                                    <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#pagamento_multiplo"><i class="ri-list-check-3"></i> Pagamento múltiplo</button><br>
                                    <button type="button" class="btn btn-dark w-100 mt-1" data-bs-toggle="modal" data-bs-target="#lista_precos"><i class="ri-cash-line"></i> Lista de preços</button>
                                <!-- <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-muted text-uppercase fs-13 mt-0" title="Average Revenue">Pagamento Multiplo</h5>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <button type="button" class="avatar-title text-bg-warning rounded rounded-3 fs-3 widget-icon-box-avatar shadow pagamento_multiplo" data-bs-toggle="modal" data-bs-target="#pagamento_multiplo">
                                        <i class=" ri-list-check-3"></i>
                                    </button>
                                </div> -->
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <div class="col">
                    <div class="card widget-icon-box div-pagamento" style="height: 93%">
                        <div class="card-body">
                            <div class="mt-2">

                                <a class="btn btn-danger bbtn-sm" href="{{ route('pre-venda.index')}}" style="margin-top: -20px">
                                    <i class="ri-arrow-left-s-line"></i>
                                    Sair da Pré Venda
                                </a><br><br>

                                <button type="button" disabled class="btn btn-success w-100" id="salvar_pre_venda">
                                    <i class="ri-checkbox-line"></i>
                                    @if(isset($item))
                                    Atualizar Pré Venda
                                    @else
                                    Finalizar Pré Venda
                                    @endif
                                </button>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
            </div>
        </div>
    </div>
</div>
</div>

@include('modals._lista_precos')

@include('modals._pagamento_multiplo', ['not_submit' => true])
@include('modals._funcionario', ['not_submit' => true])
@include('modals._variacao', ['not_submit' => true])

@include('modals._cartao_credito', ['not_submit' => true])
@include('modals._cliente', ['cashback' => 0])

@section('js')

<script type="text/javascript">
    var senhaAcao = ''
    @if(isset($config))
    senhaAcao = {{ $config != null ? $config->senha_manipula_valor : '' }}
    @endif
</script>
<script src="/js/pre_venda.js"></script>

<script type="text/javascript">
    @if(Session::has('codigo'))
    window.open(path_url + 'pre-venda/imprimir/' + '{{ Session::get('codigo') }}')
    @endif
</script>

<script src="/js/novo_cliente.js"></script>
<script type="text/javascript">

    $('.btn-novo-cliente').click(() => {
        $('.modal-select-cliente .btn-close').trigger('click')
        $('#modal_novo_cliente').modal('show')

    })
</script>
@endsection



