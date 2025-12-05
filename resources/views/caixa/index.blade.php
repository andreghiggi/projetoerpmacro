@extends('layouts.app', ['title' => 'Caixa'])
@section('css')
<link rel="stylesheet" type="text/css" href="/css/caixa.css">
@endsection
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            @if($item->status == 0)
            <a href="{{ route('caixa.create') }}" class="btn btn-success">
                <i class="ri-add-circle-fill"></i>
                Abrir Caixa
            </a>
            @else
            <div class="card-body">
                <div class="row">

                    <div class="row  mt-1">

                        @if(__countLocalAtivo() > 1)
                        <h5 class="mt-2">Local: <strong class="text-danger">{{ $item->localizacao ? $item->localizacao->descricao : '' }}</strong></h5>
                        @endif

                        @if($item->contaEmpresa)
                        <h5 class="mt-2">Conta: <strong class="text-muted">{{ $item->contaEmpresa->nome }}</strong></h5>
                        @endif

                        <div class="col-12 col-md-4">

                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="ri-user-line"></i>
                                </div>
                                <div class="info-title">Usuário</div>
                                <div class="info-value">{{ $item->usuario->name }}</div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="ri-calendar-line"></i>
                                </div>
                                <div class="info-title">Data de Abertura</div>
                                <div class="info-value">{{ __data_pt($item->created_at) }}</div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </div>
                                <div class="info-title">Valor de Abertura</div>
                                <div class="info-value">{{ __moeda($item->valor_abertura) }}</div>
                            </div>
                        </div>

                        @if($item->contaEmpresa)
                        <div class="col">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </div>
                                <div class="info-title">Conta</div>
                                <div class="info-value">{{ $item->contaEmpresa->nome }}</div>
                            </div>
                        </div>
                        @endif

                    </div>

                    @if($item->observacao)
                    <div class="row">
                        <div class="col-12 m-3 text-primary">
                            {{ $item->observacao }}
                        </div>
                    </div>
                    @endif

                </div>

                <div class="card mt-3">

                    <div class="card-header total-header">
                        <i class="ri-bar-chart-line"></i> Total por Tipo de Pagamento Vendas
                    </div>

                    <div class="card-body py-3">
                        <div class="row g-3">

                            @foreach($somaTiposPagamento as $key => $tp)
                            @if($tp > 0)
                            <div class="col-md-4 col-sm-6">
                                <div class="tp-card">
                                    <div class="tp-title">
                                        {{App\Models\Nfce::getTipoPagamento($key)}}
                                    </div>
                                    @php
                                    if($key == '01') $somaDinheiro = $tp;
                                    @endphp
                                    <div class="tp-value">R$ {{ __moeda($tp) }}</div>
                                </div>
                            </div>

                            @endif
                            @endforeach

                        </div>
                    </div>

                </div>

                <div class="card mt-3 mov-card">

                    <div class="card-header mov-header">
                        <i class="ri-shopping-bag-line"></i> Movimentações de Vendas
                    </div>

                    <div class="card-body mov-body p-0">

                        <table class="table mov-table mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>TIPO</th>
                                    <th>CLIENTE</th>
                                    <th>DATA</th>
                                    <th>FORMA(S) DE PAGAMENTO</th>
                                    <th>VALOR TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vendas as $i)
                                <tr>
                                    <td>{{ $i->numero_sequencial }}</td>
                                    <td>{{ $i->tipo }}</td>
                                    <td>{{ $i->cliente ? $i->cliente->razao_social : 'NÃO IDENTIFICADO' }}</td>
                                    <td>{{ __data_pt($i->created_at) }}</td>
                                    <td>
                                        @foreach($i->fatura as $f)
                                        <span class="pg-badge">{{ App\Models\Nfce::getTipoPagamento($f->tipo_pagamento) }}</span> 
                                        <span class="fw-bold">R$ {{ __moeda($f->valor) }}</span><br>
                                        @endforeach
                                    </td>
                                    <td class="text-success fw-bold">R$ {{ __moeda($i->total) }}</td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nenhum registro</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-3 mov-card-compra">

                    <div class="card-header mov-compra-header">
                        <i class="ri-shopping-bag-2-fill"></i> Movimentações de Compras
                    </div>

                    <div class="card-body mov-body p-0">

                        <table class="table mov-table mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>FORNECEDOR</th>
                                    <th>DATA</th>
                                    <th>FORMA(S) DE PAGAMENTO</th>
                                    <th>VALOR TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($compras as $i)
                                <tr>
                                    <td>{{ $i->numero_sequencial }}</td>
                                    <td>{{ $i->fornecedor ? $i->fornecedor->razao_social : 'NÃO IDENTIFICADO' }}</td>
                                    <td>{{ __data_pt($i->created_at) }}</td>
                                    <td>
                                        @foreach($i->fatura as $f)
                                        <span class="pg-badge">{{ App\Models\Nfce::getTipoPagamento($f->tipo_pagamento) }}</span> 
                                        <span class="fw-bold">R$ {{ __moeda($f->valor) }}</span><br>
                                        @endforeach
                                    </td>
                                    <td class="text-success fw-bold">R$ {{ __moeda($i->total) }}</td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nenhum registro</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-3 rec-card">

                    <div class="card-header rec-header">
                        <i class="ri-money-dollar-circle-line"></i> Movimentações de Recebimentos
                    </div>

                    <div class="card-body rec-body">

                        <table class="table rec-table mb-0">
                            <thead class="table-dark">
                                <tr>

                                    <th>CLIENTE</th>
                                    <th>DESCRIÇÃO</th>
                                    <th>DATA VENCIMENTO</th>
                                    <th>DATA RECEBIMENTO</th>
                                    <th>FORMA DE PAGAMENTO</th>
                                    <th>VALOR TOTAL</th>
                                    <th>VALOR RECEBIDO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($contasReceber as $c)
                                <tr>
                                    <td>{{ $c->cliente ? $c->cliente->razao_social : '--' }}</td>
                                    <td>{{ $c->descricao ?? '--' }}</td>
                                    <td>{{ __data_pt($c->data_vencimento, 0) }}</td>
                                    <td>{{ __data_pt($c->data_recebimento, 0) }}</td>
                                    <td>
                                        {{ App\Models\Nfce::getTipoPagamento($c->tipo_pagamento) }}
                                    </td>
                                    <td>{{ __moeda($c->valor_integral) }}</td>
                                    <td>{{ __moeda($c->valor_recebido) }}</td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nenhum registro</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>


                <div class="card mt-3 rec-card">

                    <div class="card-header rec-header bg-secondary">
                        <i class="ri-money-dollar-circle-line"></i> Movimentações de Pagamentos
                    </div>

                    <div class="card-body rec-body">

                        <table class="table rec-table mb-0">
                            <thead class="table-dark">
                                <tr>

                                    <th>FORNECEDOR</th>
                                    <th>DESCRIÇÃO</th>
                                    <th>DATA VENCIMENTO</th>
                                    <th>DATA RECEBIMENTO</th>
                                    <th>FORMA DE PAGAMENTO</th>
                                    <th>VALOR TOTAL</th>
                                    <th>VALOR RECEBIDO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($contasPagar as $c)
                                <tr>
                                    <td>{{ $c->fornecedor ? $c->fornecedor->razao_social : '--' }}</td>
                                    <td>{{ $c->descricao ?? '--' }}</td>
                                    <td>{{ __data_pt($c->data_vencimento, 0) }}</td>
                                    <td>{{ __data_pt($c->data_pagamento, 0) }}</td>
                                    <td>
                                        {{ App\Models\Nfce::getTipoPagamento($c->tipo_pagamento) }}
                                    </td>
                                    <td>{{ __moeda($c->valor_integral) }}</td>
                                    <td>{{ __moeda($c->valor_pago) }}</td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nenhum registro</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>

                <div class="card mt-3 devol-card">

                    <div class="card-header devol-header">
                        <i class="ri-arrow-go-back-line"></i> Devoluções e Trocas
                    </div>

                    <div class="card-body devol-body">

                        <table class="table devol-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>VENDA</th>
                                    <th>CLIENTE</th>
                                    <th>DATA</th>
                                    <th>FORMA DE PAGAMENTO</th>
                                    <th>VALOR TROCA/DEVOLUÇÃO</th>
                                    <th>VALOR ORIGINAL DA VENDA</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($trocas as $t)
                                <tr>
                                    <td>{{ $t->numero_sequencial }}</td>
                                    <td>{{ $t->nfe ? ($t->nfe ? $t->nfe->numero_sequencial : '--') : ($t->nfce ? $t->nfce->numero_sequencial : '--') }}</td>
                                    <td>{{ $t->nfce ? ($t->nfce->cliente ? $t->nfce->cliente->razao_social : '--') : ($t->nfe ? $t->nfe->cliente->razao_social : '--') }}</td>
                                    <td>{{ __data_pt($t->created_at) }}</td>
                                    <td>
                                        {{ App\Models\Nfce::getTipoPagamento($t->tipo_pagamento) }}
                                    </td>
                                    <td>{{ __moeda($t->valor_troca) }}</td>
                                    <td>{{ __moeda($t->valor_original) }}</td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nenhum registro</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="card mt-3 demo-card">

                    <div class="card-header demo-header">
                        <i class="ri-file-list-2-line"></i> Demonstrativo Financeiro do Caixa
                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-md-4">
                                <div class="demo-box">
                                    <div class="demo-title entrada">
                                        <i class="ri-add-circle-line"></i> Entradas
                                    </div>

                                    <div class="demo-row">
                                        Valor de Abertura
                                        <span class="demo-value green">R$ {{ __moeda($item->valor_abertura) }}</span>
                                    </div>
                                    <div class="demo-row">
                                        Vendas
                                        <span class="demo-value green">R$ {{ __moeda($somaVendas) }}</span>
                                    </div>
                                    <div class="demo-row">
                                        Contas Recebidas
                                        <span class="demo-value green">R$ {{ __moeda($somaContasReceber) }}</span>
                                    </div>
                                    <div class="demo-row">
                                        <span>Suprimentos <i class="ri-information-line icon-modal" data-bs-toggle="modal" data-bs-target="#modalSuprimentos"></i></span>
                                        <span class="demo-value green">R$ {{ __moeda($somaSuprimentos) }}</span>
                                    </div>

                                    <div class="demo-row">
                                        Trocas - Entradas (Diferenças pagas pelo cliente)
                                        <span class="demo-value green">R$ {{ __moeda($trocasPagasPorCliente) }}</span>
                                    </div>

                                    <div class="demo-total">
                                        Total de Entradas
                                        @php $totalEntradas = $trocasPagasPorCliente + $somaSuprimentos + $item->valor_abertura + $somaVendas + $somaContasReceber; @endphp
                                        <span class="demo-value green">R$ {{__moeda($totalEntradas) }}</span>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="demo-box">
                                    <div class="demo-title saida">
                                        <i class="ri-close-circle-line"></i> Saídas
                                    </div>

                                    
                                    <div class="demo-row">
                                        Contas Pagas
                                        <span class="demo-value red">R$ {{ __moeda($somaContasPagar) }}</span>
                                    </div>
                                    <div class="demo-row">
                                        Compras
                                        <span class="demo-value red">R$ {{ __moeda($somaCompras) }}</span>
                                    </div>

                                    <div class="demo-row">
                                        <span>Sangrias <i class="ri-information-line icon-modal" data-bs-toggle="modal" data-bs-target="#modalSangrias"></i></span>
                                        <span class="demo-value red">R$ {{ __moeda($somaSangrias) }}</span>
                                    </div>
                                    <div class="demo-row">
                                        Trocas - Saídas (Diferenças pagas ao cliente)
                                        <span class="demo-value red">R$ {{ __moeda($trocasPagasAoCliente) }}</span>
                                    </div>

                                    <div class="demo-row" style="height: 45px;">
                                    </div>

                                    <div class="demo-total">
                                        Total de Saídas
                                        @php $totalSaidas = $somaCompras + $somaContasPagar + $somaSangrias + $trocasPagasAoCliente; @endphp
                                        <span class="demo-value red">R$ {{ __moeda($totalSaidas) }}</span>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="demo-box">
                                    <div class="demo-title saldo">
                                        <i class="ri-money-dollar-circle-line"></i> Saldos e Pendências
                                    </div>

                                    <div class="demo-row">
                                        Saldo em Caixa
                                        <span class="demo-value purple">R$ {{ __moeda($totalEntradas - $totalSaidas) }}</span>
                                    </div>

                                    <div class="demo-row">
                                        Vendas Pendentes (Crediário)
                                        <span class="demo-value yellow">R$ {{ __moeda($somaPendentesCrediario) }}</span>
                                    </div>
                                    <div class="demo-row">
                                        Vendas Pendentes (Boleto)
                                        <span class="demo-value yellow">R$ {{ __moeda($somaPendentBoleto) }}</span>
                                    </div>
                                    <div class="demo-row">
                                        Vendas Pendentes (Crédito Loja)
                                        <span class="demo-value yellow">R$ {{ __moeda($somaPendentesCreditoLoja) }}</span>
                                    </div>
                                    <div class="demo-row" style="height: 48px;">
                                    </div>

                                    <div class="demo-total">
                                        Total Vendas Pendentes
                                        <span class="demo-value yellow">R$ 
                                            {{ __moeda($somaPendentesCrediario + $somaPendentBoleto + $somaPendentesCreditoLoja) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="col-md-3 m-3">

                    <button class="btn btn-primary w-100 mt-2" data-bs-toggle="modal" data-bs-target="#sangria_caixa">
                        <i class="ri-arrow-up-circle-line"></i>
                        Sangria
                    </button>

                    <button class="btn btn-success w-100 mt-2" data-bs-toggle="modal" data-bs-target="#suprimento_caixa">
                        <i class="ri-arrow-down-circle-line"></i>
                        Suprimento
                    </button>

                    @if(sizeof($contasEmpresa) == 0)
                    <button class="btn btn-danger w-100 mt-2" data-bs-toggle="modal" data-bs-target="#fechamento_caixa">
                        <i class="ri-close-circle-fill"></i>
                        Fechar Caixa
                    </button>
                    @else
                    <a class="btn btn-danger w-100 mt-2" href="{{ route('caixa.fechar-conta', [$item->id]) }}">
                        <i class="ri-close-circle-fill"></i>
                        Fechar Caixa
                    </a>
                    @endif

                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSuprimentos" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">Suprimentos do Caixa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Observação</th>
                                @if($item->contaEmpresa)
                                <th>Conta</th>
                                @endif
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suprimentos as $s)
                            <tr>
                                <td>{{ __data_pt($s->created_at) }}</td>
                                <td>{{ __moeda($s->valor) }}</td>
                                <td>
                                    {{ $s->observacao }}
                                </td>
                                @if($s->contaEmpresa)
                                <td>
                                    {{ $s->contaEmpresa->nome }}
                                </td>
                                @endif

                                <td>
                                    <form action="{{ route('suprimento.destroy', $s->id) }}" method="post" id="form-suprimento-{{$s->id}}">
                                        @method('delete')
                                        @csrf
                                        <a target="_blank" href="{{ route('suprimento.print', [$s->id]) }}" class="btn btn-dark btn-sm">
                                            <i class="ri-printer-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm btn-delete"><i class="ri-delete-bin-line"></i></button>
                                    </form>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">Nenhum registro</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalSangrias" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Sangrias do Caixa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-dark">

                            <tr>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Observação</th>
                                @if($item->contaEmpresa)
                                <th>Conta</th>
                                @endif
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sangrias as $s)
                            <tr>
                                <td>{{ __data_pt($s->created_at) }}</td>
                                <td>{{ __moeda($s->valor) }}</td>
                                <td>
                                    {{ $s->observacao }}
                                </td>
                                @if($s->contaEmpresa)
                                <td>
                                    {{ $s->contaEmpresa->nome }}
                                </td>
                                @endif
                                <td>
                                    <form action="{{ route('sangria.destroy', $s->id) }}" method="post" id="form-sangria-{{$s->id}}">
                                        @method('delete')
                                        @csrf
                                        <a target="_blank" href="{{ route('sangria.print', [$s->id]) }}" class="btn btn-dark btn-sm">
                                            <i class="ri-printer-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm btn-delete"><i class="ri-delete-bin-line"></i></button>
                                    </form>

                                </td>


                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">Nenhum registro</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>

        </div>
    </div>
</div>

@include('modals._fechamento_caixa', ['not_submit' => true])
@include('modals._suprimento_caixa', ['not_submit' => true])
@include('modals._sangria_caixa', ['not_submit' => true])

@section('js')
<script type="text/javascript" src="/js/controla_conta_empresa.js"></script>
@endsection
@endsection
