@extends('layouts.app', ['title' => 'Cotação #' . $item->referencia])
@section('css')
<style type="text/css">
    @page { size: auto;  margin: 0mm; }

    @media print {
        .print{
            margin: 10px;
        }
    }
</style>
@endsection
@section('content')
<div class="mt-1 print">
    <div class="row">

        <div class="card">
            <div class="card-body">

                <!-- Invoice Logo-->
                <div class="clearfix">

                    <div class="float-end">
                        <h4 class="m-0">{{ $item->fornecedor->info }}</h4>
                    </div>
                </div>

                <!-- Invoice Detail-->
                <div class="row">
                    <div style="margin-top: -25px;" class="d-print-none">
                        <a href="{{ route('cotacoes.index') }}" class="btn btn-danger btn-sm px-3">
                            <i class="ri-arrow-left-double-fill"></i>Voltar
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <div class=" mt-3">
                            <p><b>Referencia: <strong class="text-success">#{{ $item->referencia }}</strong></b></p>
                            <p><b>Responsável: <strong class="text-success">{{ $item->responsavel }}</strong></b></p>
                            <p><b>Estado: 
                                @if($item->estado == 'aprovada')
                                <span class="bg-success text-white p-2" style="border-radius: 5px;">Aprovada</span>
                                @elseif($item->estado == 'rejeitada')
                                <span class="bg-danger text-white p-2" style="border-radius: 5px;">Rejeitada</span>
                                @elseif($item->estado == 'respondida')
                                <span class="bg-primary text-white p-2" style="border-radius: 5px;">Respondida</span>
                                @else
                                <span class="bg-info text-white p-2" style="border-radius: 5px;">Nova</span>
                                @endif
                            </b></p>
                            
                        </div>

                    </div><!-- end col -->
                    <div class="col-sm-4 offset-sm-2">
                        <div class="mt-3 float-sm-end">

                            <p class="fs-15"><strong>Data de cadastro: </strong>{{ __data_pt($item->created_at, 1) }}</p>
                            <p class="fs-15"><strong>Data de resposta: </strong>{{ __data_pt($item->data_resposta, 1) }}</p>
                            <p class="fs-15"><strong>Previsão de entrega: </strong>{{ __data_pt($item->previsao_entrega, 0) }}</p>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row mt-4">
                    <div class="col-8">

                    </div>

                    <div class="col-4">
                        <div class="text-sm-end">
                            {{ $item->codigo_barras }}
                        </div>
                    </div> <!-- end col-->
                </div>    
                <!-- end row -->        

                <div class="row">

                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-centered table-hover table-borderless mb-0 mt-3">
                                <thead class="border-top border-bottom bg-light-subtle border-light">
                                    <tr>

                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Valor unitário</th>
                                        <th>Subtotal</th>
                                        <th>Observação do item</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->itens as $i)
                                    <tr>
                                        <td>{{ $i->produto->nome }}</td>
                                        @php
                                        $casasDecimais = 2;
                                        if($i->produto->unidade == 'UN'){
                                            $casasDecimais = 0;
                                        }
                                        @endphp
                                        <td>{{ number_format($i->quantidade, $casasDecimais) }}</td>
                                        <td>{{ __moeda($i->valor_unitario) }}</td>
                                        <td>{{ __moeda($i->sub_total) }}</td>
                                        <td>{{ $i->observacao }}</td>
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->
                    </div> <!-- end col -->
                </div>

                @if(sizeof($item->fatura) > 0)
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <br>
                        <div class="table-responsive">
                            <table class="table table-sm table-centered table-hover table-borderless mb-0 mt-3">
                                <thead class="border-top border-bottom bg-light-subtle border-light">
                                    <tr>

                                        <th>Vencimento</th>
                                        <th>Tipo de pagamento</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->fatura as $i)
                                    <tr>
                                        <td>{{ __data_pt($i->data_vencimento, 0) }}</td>
                                        <td>{{ $i->getTipoPagamento() }}</td>
                                        <td>{{ __moeda($i->valor) }}</td>
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->
                    </div> <!-- end col -->
                </div>
                @endif
                <!-- end row -->

                <div class="row">
                    <div class="col-sm-6">
                        <div class="clearfix pt-3">

                        </div>
                    </div> <!-- end col -->
                    <div class="col-sm-6">
                        <div class="float-end mt-3">
                            <p><b>Valor dos produtos: </b> 
                                <span class="float-end ml-1" style="margin-left: 3px"> 
                                    R$ {{ __moeda($item->itens->sum('sub_total')) }}
                                </span>
                            </p>
                            <p><b>Desconto: </b> 
                                <span class="float-end ml-1" style="margin-left: 3px"> 
                                    R$ {{ __moeda($item->desconto) }}
                                </span>
                            </p>
                            <p><b>Valor do frete: </b> 
                                <span class="float-end ml-1" style="margin-left: 3px"> 
                                    R$ {{ __moeda($item->valor_frete) }}
                                </span>
                            </p>
                            <p><b>Valor total: </b> 
                                <span class="float-end ml-1" style="margin-left: 3px"> 
                                    R$ {{ __moeda($item->valor_total) }}
                                </span>
                            </p>
                            @if($item->observacao)
                            <p><b>Observação: </b> 
                                <span class="float-end ml-1" style="margin-left: 3px"> 
                                    {{ $item->observacao }}
                                </span>
                            </p>
                            @endif
                            @if($item->observacao_resposta)
                            <p><b>Observação de resposta: </b> 
                                <span class="float-end ml-1" style="margin-left: 3px"> 
                                    {{ $item->observacao_resposta }}
                                </span>
                            </p>
                            @endif
                            @if($item->observacao_frete)
                            <p><b>Observação do frete: </b> 
                                <span class="float-end ml-1" style="margin-left: 3px"> 
                                    {{ $item->observacao_frete }}
                                </span>
                            </p>
                            @endif

                        </div>
                        <div class="clearfix"></div>
                    </div> <!-- end col -->
                </div>
                <!-- end row-->

                <div class="d-print-none mt-4">
                    <div class="text-end">
                        @if($cotacaoComCompra == null)
                        @if($item->estado != 'aprovada')
                        <a href="{{ route('cotacoes.purchase', [$item->id]) }}" class="btn btn-dark"><i class="ri-bookmark-fill"></i> Gerar compra</a>
                        @endif

                        @endif

                        @if($item->nfe_id)
                        <a class="btn btn-success" href="{{ route('nfe.show', $item->nfe_id) }}">
                            <i class="ri-file-text-line"></i>
                            Ver NFe
                        </a>
                        @endif

                        <a href="javascript:window.print()" class="btn btn-primary"><i class="ri-printer-line"></i> Imprimir</a>

                    </div>
                </div>
                @if($cotacaoComCompra != null)
                <p class="text-danger">
                    Não é possível gerar compra para esta cotação, <strong>{{ $cotacaoComCompra->fornecedor->info }}</strong> já foi escolhido como fornecedor.
                </p>
                @endif
                <!-- end buttons -->

            </div>
        </div>
    </div>
</div>
@endsection