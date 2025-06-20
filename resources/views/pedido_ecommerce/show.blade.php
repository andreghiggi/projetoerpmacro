@extends('layouts.app', ['title' => 'Pedido Ecommerce'])
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

<div class="card mt-1 print">
    <div class="card-body">
        <div class="pl-lg-4">

            <div class="ms">
                {!! $item->_estado() !!}
                <div class="mt-3 d-print-none" style="text-align: right;">
                    <a href="{{ route('pedidos-ecommerce.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                <div class="row mb-2">

                    <div class="col-md-3 col-6">
                        <h5><strong class="text-danger">#{{ $item->hash_pedido }}</strong></h5>
                    </div>
                    <div class="col-md-3 col-6">
                        <h5>Data do pedido: <strong class="text-primary">{{ __data_pt($item->created_at) }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Data de entrega: <strong class="text-primary">{{ $item->data_entrega ? __data_pt($item->data_entrega, 0) : '--' }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Código de rastreamento: <strong class="text-primary">{{ $item->codigo_rastreamento ? $item->codigo_rastreamento : '--' }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Valor Total: <strong class="text-primary">R$ {{ __moeda($item->valor_total) }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Valor Frete: <strong class="text-primary">R$ {{ __moeda($item->valor_frete) }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Status de pagamento: 
                            @if($item->status_pagamento == 'approved')
                            <strong class="text-success">
                                Aprovado
                            </strong>
                            @elseif($item->status_pagamento == 'pending')
                            <strong class="text-danger">
                                Pendente
                            </strong>
                            @else
                            <strong class="text-warning">
                                Pendente depósito
                            </strong>
                            @endif
                        </h5>
                    </div>

                    @if($item->comprovante != null)
                    <div class="col-md-3 col-6">
                        <a class="btn btn-dark" target="_blank" href="/uploads/comprovantes/{{ $item->comprovante }}">ver comprovante</a>
                    </div>
                    @endif

                </div>

                <a href="{{ route('pedidos-ecommerce.alterar-estado', $item->id) }}" class="btn btn-info btn-sm d-print-none" href=""><i class="ri-refresh-line"></i>
                    Alterar estado
                </a>
                <a class="btn btn-primary btn-sm d-print-none" href="javascript:window.print()" ><i class="ri-printer-line d-print-none"></i>
                    Imprimir
                </a>
                @if($item->nfe_id == 0)
                <a class="btn btn-success btn-sm d-print-none" href="{{ route('pedidos-ecommerce.gerar-nfe', $item->id) }}">
                    <i class="ri-file-text-line"></i>
                    Gerar NFe
                </a>
                @else
                <a class="btn btn-success btn-sm d-print-none" href="{{ route('nfe.show', $item->nfe_id) }}">
                    <i class="ri-file-text-line"></i>
                    Ver NFe
                </a>
                @endif

                <div class="d-print-none mt-2">
                    @if($item->tipo_pagamento == 'boleto')
                    <a href="{{ $item->link_boleto }}" target="_blank">
                        <i class="ri-links-fill"></i> Link do boleto
                    </a>
                    @endif

                    @if($item->tipo_pagamento == 'pix')
                    <p>PIX: {{ $item->qr_code }}</p>
                    @endif
                </div>

                <h5>Transação ID: <strong class="text-primary">{{ $item->transacao_id }}</strong></h5>
            </div>

            <div class="row mt-2">
                <h4>Itens do pedido</h4>
                <div class="table-responsive-sm">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Valor unitário</th>
                                <th>Sub total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->itens as $i)
                            <tr>
                                <td>{{ $i->descricao() }}</td>
                                <td>{{ number_format($i->quantidade, 0) }}</td>
                                <td>{{ __moeda($i->valor_unitario) }}</td>
                                <td>{{ __moeda($i->sub_total) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-4">

                <div class="col-md-6 col-12">
                    <h4>Cliente: <strong>{{ $item->cliente->info }}</strong></h4>
                    <h4>Email: <strong>{{ $item->cliente->email }}</strong></h4>
                    <h4>Telefone: <strong>{{ $item->cliente->telefone }}</strong></h4>
                    <h4>Data de cadastro: <strong>{{ __data_pt($item->cliente->created_at) }}</strong></h4>
                    <h4>Tipo do frete: <strong>{{ $item->tipo_frete }}</strong></h4>
                </div>
                <div class="col-md-6 col-12">
                    <h4>Rua: <strong>{{ $item->rua_entrega ? $item->rua_entrega : '--' }}</strong></h4>
                    <h4>Número: <strong>{{ $item->numero_entrega ? $item->numero_entrega : '--' }}</strong></h4>
                    <h4>Bairro: <strong>{{ $item->bairro_entrega ? $item->bairro_entrega : '--' }}</strong></h4>
                    <h4>CEP: <strong>{{ $item->cep_entrega ? $item->cep_entrega : '--' }}</strong></h4>
                    <h4>Cidade: <strong>{{ $item->cidade_entrega ? $item->cidade_entrega : '--' }}</strong></h4>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('js')

@endsection
