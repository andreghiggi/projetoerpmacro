@extends('layouts.app', ['title' => 'Detalhes da Venda'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Detalhes da Venda</h4>
                <div style="text-align: right; margin-top: -35px;">
                    <a href="{{ route('frontbox.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                <hr class="mt-3">

                <a class="btn btn-dark mb-2" title="Imprimir" target="_blank" href="{{ route('frontbox.imprimir-nao-fiscal', [$data->id]) }}">
                    Imprimir
                    <i class="ri-printer-line"></i>
                </a>
                <div class="">
                    <h4>Cliente: <strong style="color: steelblue">{{ $data->cliente_id ? $data->cliente->razao_social : 'Consumidor Final'}}</strong></h4>

                    @if($data->user)
                    <h5>Usuário: <strong class="text-">{{ $data->user->name}}</strong></h5>
                    @endif
                </div>
                <hr>
                <div class="col-lg-12 mt-4">
                    <div class="table-responsive">
                        <h5>Produtos:</h5>
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Valor</th>
                                    <th>Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data->itens as $item)
                                <tr>
                                    <td>{{ $item->produto->numero_sequencial }}</td>
                                    <td>{{ $item->produto->nome }}</td>
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
                        <h5 class="mt-2">TOTAL: R${{ __moeda($data->itens->sum('sub_total')) }}</h5>
                    </div>

                    @if(sizeof($data->itensServico) > 0)
                    <hr>
                    <div class="table-responsive">
                        <h5>Serviços:</h5>
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Serviço</th>
                                    <th>Quantidade</th>
                                    <th>Valor</th>
                                    <th>Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data->itensServico as $item)
                                <tr>
                                    <td>{{ $item->servico->numero_sequencial }}</td>
                                    <td>{{ $item->servico->nome }}</td>
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
                        <h5 class="mt-2">TOTAL: R${{ __moeda($data->itensServico->sum('sub_total')) }}</h5>
                    </div>
                    @endif
                    <div class="mt-5 col-md-6 col-12">
                        <h5>Forma de Pagamento:</h5>
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
                                    <td colspan="7" class="text-center">Nada encontrado</td>
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
@endsection
