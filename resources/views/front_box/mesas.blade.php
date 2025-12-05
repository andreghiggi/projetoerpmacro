@extends('layouts.app', ['title' => 'Mesas/Comandas'])
@section('css')

<link href="/css/mesas_pdv.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .swal-button--confirm {
        background-color: #159488 !important;
        color: white !important;
    }
</style>
@endsection
@section('content')

<form id="form-delete-comanda" action="{{ route('pedidos-cardapio.delete') }}" method="post">
    @method('delete')
    @csrf
    <input type="hidden" id="comanda_delete_id" name="comanda_id" value="{{ $comanda ? $comanda->id : 0 }}">
    <input type="hidden" name="redirect_mesas_pdv" value="1">
</form>

{!!Form::open()
->post()
->route('frontbox.store')->id('form-comanda')
!!}

<input type="hidden" id="abertura" value="{{ $abertura }}" name="">
<input type="hidden" id="numero_comanda" value="{{ $numeroComanda }}" name="">
<input type="hidden" id="comanda_id" value="{{ $comanda ? $comanda->id : 0 }}" name="">
<input type="hidden" id="local_id" value="{{ $caixa->localizacao->id }}">
<input type="hidden" id="alerta_sonoro" value="{{ $config ? $config->alerta_sonoro : 0 }}">
<input type="hidden" id="impressao_sem_janela_cupom" value="{{ $config ? $config->impressao_sem_janela_cupom : 0 }}">
<input type="hidden" id="tipo_divisao_pizza" value="{{ $configCardapio != null ? $configCardapio->valor_pizza : 'divide' }}">

<div class="row" style="margin-top: 5px;">
    <div class="col-xl-3">
        <div class="card">
            <div class="card-body scroll-box">
                <h5 class="text-center">Mesas/Comandas</h5>


                <div class="col-md-12">
                    {!! Form::text('pesquisa_comanda', 'Busca comanda ou mesa')->attrs(['class' => 'not-enter']) !!}
                </div>
                <div class="row comandas">
                    @foreach($comandas as $c)
                    <div class="col-12 col-xl-6 d-flex comanda comanda-{{ $c['comanda'] }}">
                        <a href="{{ route('frontbox.mesas', ['comanda='.$c['comanda']]) }}" class="vaga {{ $c['status'] ? 'bg-danger' : 'bg-primary' }} text-center">
                            <label class="numero_comanda">{{ $c['comanda'] }}</label><br> <small class="status-comanda">{{ $c['status'] ? 'Ocupada' : 'Livre'}}</small>
                            <br> 
                            <strong class="total-comanda">R$ {{ __moeda($c['total']) }}</strong>
                            <small>{{ $c['mesa'] }}</small>
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body scroll-box">
                <h5 class="text-center">Cárdapio</h5>

                <h6>Categorias</h6>

                <div class="row">
                    <div class="col-md-12 categorias">
                        <button type="button" class="btn-outline-primary btn list-categoria active" data-id="0">TODOS</button>
                        @foreach($categorias as $c)
                        <button type="button" class="btn-outline-primary btn list-categoria categoria-{{$c->id}}" data-id="{{$c->id}}">{{ $c->nome }}</button>
                        @endforeach
                        
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">

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
                                {!! Form::tel('quantidade', 'Quantidade')->attrs(['class' => 'qtd not-enter']) !!}
                            </div>
                            <div class="col-md-2">
                                {!! Form::tel('valor_unitario', 'Valor Unitário')->attrs(['class' => 'moeda value_unit not-enter']) !!}
                            </div>
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-12">
                                        <br>
                                        <button class="btn btn-success btn-add-item w-100" type="button" style="margin-left: 0px">
                                            <i class="ri-add-circle-line"></i>
                                            Adicionar
                                        </button>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        {!! Form::text('pesquisa', 'Busca rápida de produtos')->attrs(['class' => '']) !!}
                    </div>
                </div>

                <div class="row mt-1 div-produtos">
                    <!-- produtos -->

                    @foreach($produtos as $p)
                    <div class="col-md-2 produto" onclick="addProduto('{{$p->id}}')">

                        <div class="card" style="height: 200px">

                            <span class="stock-item"><b>{{ $p->estoqueAtual() }} {{ $p->unidade }}</b></span>
                            @if($p->precoComPromocao())
                            <div class="ribbon" aria-hidden="true">Promoção</div>
                            @endif
                            <img class="card-img-top" src="{{ $p->img }}" alt="{{ $p->nome }}">
                            <div class="card-body body-item">
                                <h4 class="card-title">{{ substr($p->nome, 0, 30) }}
                                    <i onclick="infoProduto('{{$p->id}}')" style="font-size: 16px" class="ri-information-line"></i>
                                </h4>

                                @if($p->precoComPromocao())
                                <p class="card-text text-success"> R$ {{ __moeda($p->precoComPromocao()->valor) }}</p>
                                @else
                                @if(isset($lista_id) && $lista_id)
                                @if($p->itemListaView($lista_id))
                                <p class="card-text">R$ {{ __moeda($p->itemListaView($lista_id)->valor) }}</p>
                                @endif

                                @else
                                <p class="card-text text-success">R$ {{ __moeda(__valorProdutoLocal($p, $local_id)) }}</p>
                                @endif
                                @endif
                            </div>
                            
                        </div>
                    </div>
                    @endforeach
                    <div class="mt-1 d-flex justify-content-center produtos-pagination">
                        {!! $produtos->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="card">
            <div class="card-body scroll-box">
                <h6 class="text-center">
                    PRODUTOS DA MESA/COMANDA
                    @if($numeroComanda)
                    <span class="badge bg-primary">{{ $numeroComanda }}</span>
                    @endif
                </h6>

                <div class="row">

                    <div class="container my-2 scroll-itens itens-cart">
                        <!-- itens -->
                        @if($comanda != null)
                        @if(sizeof($comanda->itens) > 0)

                        @foreach($comanda->itens as $item)
                        @php 
                        $code = rand(0,9999999999);
                        @endphp


                        <div class="d-flex justify-content-between align-items-center d-item border-bottom py-2 product-line product-line-{{$code}}">
                            <div>
                                <div class="fw-semibold text-uppercase small mb-1">{{ substr($item->produto->nome, 0, 30) }}</div>
                                <div class="text-muted small"><span class="unitario">R$ {{ __moeda($item->valor_unitario) }}</span> {{ $item->produto->unidade }}</div>
                                <div class="text-primary sub_total">R$ {{ __moeda($item->valor_unitario * $item->quantidade) }}</div>

                                <button type="button" class="btn btn-dark btn-sm btn-detalhes" style="padding: 2px; padding-left: 10px; padding-right: 10px; font-size: 12px">
                                    <i class="ri-sticky-note-line"></i>
                                    detalhes do item
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-1 btn-subtrai">-</button>
                                <input type="text" class="form-control text-center px-2 py-1 qtd" value="{{ (int)$item->quantidade }}" style="width: 60px;" readonly>
                                <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-1 btn-adiciona">+</button>
                                @if(__isAdmin())
                                <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1 ms-1 btn-remove" onclick="removeItem('{{$code}}')">×</button>
                                @endif
                            </div>

                            <input type="hidden" class="produto_id" name="produto_id[]" value="{{ $item->produto->id }}">
                            <input type="hidden" class="valor_original" value="{{ $item->produto->valor_unitario }}">
                            <input type="hidden" class="code" value="{{ $code }}">

                            <input type="hidden" class="quantidade" name="quantidade[]" value="{{ $item->quantidade }}">
                            <input type="hidden" class="valor_unitario" name="valor_unitario[]" value="{{ ($item->valor_unitario) }}">
                            <input type="hidden" class="subtotal_item" name="subtotal_item[]" value="{{ ($item->sub_total) }}">

                            <input type="hidden" class="observacao" name="observacao[]" value="{{ $item->observacao }}">
                            <input type="hidden" class="adicionais" name="adicionais[]" value="">
                            <input type="hidden" class="tamanho_id" name="tamanho_id[]" value="">
                            <input type="hidden" class="sabores" name="sabores[]" value="{{ $item->pizzas->pluck('produto_id') }}">

                        </div>

                        @endforeach
                        @endif
                        @endif
                    </div>

                </div>
                <hr>
                <div class="row totais">
                    <div class="col-md-6 div-mesa">

                        @if($comanda != null)
                        @if(!$comanda->_mesa)
                        <button type="button" class="btn btn-sm btn-dark w-100 btn-selecionar-mesa">
                            <i class="ri-tv-2-fill"></i>
                            Mesa/Cliente
                        </button>
                        @else
                        <button type="button" class="btn btn-sm btn-dark w-100 btn-selecionar-mesa">
                            <i class="ri-tv-2-fill"></i>
                            Mesa/Cliente

                        </button>
                        @endif
                        @else
                        <button type="button" class="btn btn-sm btn-dark w-100 btn-selecionar-mesa d-none">
                            <i class="ri-tv-2-fill"></i>
                            Mesa/Cliente
                        </button>
                        @endif
                    </div>
                    <div class="col-md-6 mb-">
                        <h5 class="text-end">Total: <strong class="total text-success">R$ {{ $comanda != null ? __moeda($comanda->total + $comanda->acrescimo - $comanda->desconto) : '0,00' }}</strong></h5>
                    </div>
                    <div class="col-md-6">
                        <button type="button" onclick="print()" class="btn btn-print btn-primary btn-sm w-100 {{ !$comanda ? 'disabled' : '' }}">
                            <i class="ri-printer-fill"></i>
                            Imprimir Comanda
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-danger btn-delete-comanda btn-sm w-100 {{ !$comanda ? 'disabled' : '' }}">
                            <i class="ri-close-circle-line"></i>
                            Remover Comanda
                        </button>

                    </div>

                    <div class="col-md-6 mt-1">
                        <button type="button" id="btn-finalizar" class="btn btn-success btn-sm w-100">
                            <i class="ri-checkbox-circle-fill"></i>
                            Finalizar
                        </button>
                    </div>
                    <div class="col-md-6 mt-1">
                        <a href="{{ route('frontbox.index')}}" class="btn btn-dark btn-sm w-100">
                            <i class="ri-logout-box-line"></i>
                            Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('modals._info_produto')
@include('modals._finalizar_pdv_comanda')
@include('modals._fatura_venda')
@include('modals._detalhes_item_mesas')

{!!Form::close()!!}

@include('modals._definir_mesa')


@endsection

@section('js')
<script type="text/javascript" src="/js/mesas_pdv.js"></script>
@endsection
