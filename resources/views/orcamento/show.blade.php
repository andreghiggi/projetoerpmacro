@extends('layouts.app', ['title' => 'Detalhes do Orçamento'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <h4>Detalhes da Orçamento</h4>
                <div style="text-align: right; margin-top: -35px;">

                    <a href="{{ route('orcamentos.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                    
                </div>
                <hr class="mt-3">
                <div class="row">

                    <h4>Cliente: <strong style="color: steelblue">{{ $data->cliente_id ? $data->cliente->razao_social : 'Consumidor Final'}}</strong></h4>
                    <h4>Data: <strong style="color: steelblue">{{ __data_pt($data->created_at) }}</strong></h4>

                    <h4>Total: <strong class="text-success">R$ {{ __moeda($data->total) }}</strong></h4>
                    @if($data->funcionario)
                    <h4>Funcionário: <strong class="text-success">{{ $data->funcionario->nome }}</strong></h4>
                    @endif

                </div>
                <hr>
                <div class="col-lg-12 mt-4">
                    <div class="table-responsive-sm">
                        <h5>Itens do orçamento</h5>
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
                                    <td colspan="3" class="text-center">Sem informações de pagamento</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                <a href="{{ route('orcamentos.gerar-venda', [$data->id]) }}" class="btn btn-success">
                    Gerar venda NFe
                </a>
                <a href="{{ route('orcamentos.gerar-venda-nfce', [$data->id]) }}" class="btn btn-dark">
                    Gerar venda NFCe
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
