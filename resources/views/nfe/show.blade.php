@extends('layouts.app', ['title' => $data->tpNF == 0 ? 'Detalhes da Compra' : 'Detalhes da Venda'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                @if($data->tpNF == 0)
                <h4>Detalhes da Compra</h4>
                @else
                <h4>Detalhes da Venda</h4>
                @endif
                <div style="text-align: right; margin-top: -35px;">
                    @if($data->tpNF == 0)
                    <a href="{{ url()->previous() }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                    @else
                    <a href="{{ url()->previous() }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                    @endif
                </div>
                <hr class="mt-3">

                <a class="btn btn-dark mb-2" title="Imprimir Pedido" target="_blank" href="{{ route('nfe.imprimirVenda', [$data->id]) }}">
                    Imprimir
                    <i class="ri-printer-line"></i>
                </a>
                <div class="row">
                    @if($data->tpNF == 0)
                    <h4>Fornecedor: <strong style="color: steelblue">{{ $data->fornecedor_id ? $data->fornecedor->razao_social : 'Consumidor Final'}}</strong></h4>
                    @else
                    <h4>Cliente: <strong style="color: steelblue">{{ $data->cliente_id ? $data->cliente->razao_social : 'Consumidor Final'}}</strong></h4>
                    @endif
                    <h4>Data de cadastro: <strong>{{ __data_pt($data->created_at) }}</strong></h4>
                    @if($data->chave)
                    <h4>Número NFe: <strong>{{ $data->numero }}</strong></h4>
                    <h4>Chave: <strong>{{ $data->chave }}</strong></h4>
                    <h4>Data de emissão: <strong>{{ __data_pt($data->data_emissao) }}</strong></h4>
                    @endif
                    @if(__isPlanoFiscal())
                    <h4>Estado:
                        @if($data->estado == 'aprovado')
                        <span class="text-success">Aprovado</span>
                        <a href="{{ route('nfe.download-xml', [$data->id]) }}" class="btn btn-dark">
                            <i class="ri-file-download-line"></i>
                            Download XML
                        </a>

                        <button type="button" onclick="imprimir('{{$data->id}}', '{{$data->numero}}')" class="btn btn-primary" title="Imprimir NFe">
                            <i class="ri-printer-line"></i>
                            Imprimir
                        </button>
                        @elseif($data->estado == 'cancelado')
                        <span class="text-danger">Cancelado</span>
                        @elseif($data->estado == 'rejeitado')
                        <span class="text-warning">Rejeitado</span>
                        @else
                        <span class="text-info">Novo</span>
                        @endif
                    </h4>
                    @endif

                    <h4>Total: <strong class="text-success">R$ {{ __moeda($data->total) }}</strong></h4>
                    @if($data->user)
                    <h5>Usuário: <strong class="text-">{{ $data->user->name}}</strong></h5>
                    @endif

                </div>
                <hr>
                <div class="col-lg-12 mt-4">
                    <div class="table-responsive-sm">
                        <h5>Itens da NFe</h5>
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Valor</th>
                                    <th>Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data->itens as $item)
                                <tr>
                                    <td>{{ $item->descricao() }}</td>
                                    <td>{{ $item->quantidade }}</td>
                                    <td>{{ __moeda($item->valor_unitario) }}</td>
                                    <td>{{ __moeda($item->sub_total) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-8 col-12 mt-5">
                        <h5>Fatura</h5>
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Pagamento</th>
                                    <th>Data Vencimento</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data->fatura as $item)
                                <tr>
                                    <td>{{ $item->getTipoPagamento($item->tipo_pagamento) }}</td>
                                    <td>{{ __data_pt($item->data_vencimento, 0) }}</td>
                                    <td>{{ __moeda($item->valor) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">NFe sem informações de pagamento</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-print" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Imprimir NFe <strong class="ref-numero"></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <button type="button" class="btn btn-success w-100" onclick="gerarDanfe('danfe')">
                            <i class="ri-printer-line"></i>
                            DANFE
                        </button>
                    </div>

                    <div class="col-12 col-lg-4">
                        <button type="button" class="btn btn-primary w-100" onclick="gerarDanfe('simples')">
                            <i class="ri-printer-line"></i>
                            DANFE Simples
                        </button>
                    </div>

                    <div class="col-12 col-lg-4">
                        <button type="button" class="btn btn-dark w-100" onclick="gerarDanfe('etiqueta')">
                            <i class="ri-printer-line"></i>
                            DANFE Etiqueta
                        </button>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    var IDNFE = null
    function imprimir(id, numero){
        IDNFE = id
        $('.ref-numero').text(numero)
        $('#modal-print').modal('show')
    }

    function gerarDanfe(tipo){
        if(tipo == 'danfe'){
            window.open('/nfe/imprimir/'+IDNFE)
        }else if(tipo == 'simples'){
            window.open('/nfe/danfe-simples/'+IDNFE)
        }else{
            window.open('/nfe/danfe-etiqueta/'+IDNFE)
        }
        $('#modal-print').modal('hide')
    }
</script>
@endsection

