@extends('layouts.app', ['title' => 'Histórico do cliente'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <hr class="mt-3">
                <h5>Histórico do cliente: <strong class="text-primary">{{ $item->info }}</strong></h5>

                <div id="basicwizard">
                    <ul class="nav nav-pills nav-justified form-wizard-header mb-4 m-2">
                        <li class="nav-item">
                            <a href="#tab-vendas" data-bs-toggle="tab" data-toggle="tab"  class="nav-link rounded-0 py-1"> 
                                <i class="ri-stack-fill fw-normal fs-18 align-middle me-1"></i>
                                <span class="d-none d-sm-inline">Vendas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-produtos" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 py-1">
                                <i class="ri-box-3-line fs-18 align-middle me-1"></i>
                                <span class="d-none d-sm-inline">Produtos vendidos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-faturas" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 py-1">
                                <i class="ri-wallet-line fs-18 align-middle me-1"></i>
                                <span class="d-none d-sm-inline">Faturas</span>
                            </a>
                        </li>
                    </ul>
                    <!--  -->
                    <div class="tab-content b-0 mb-0">
                        <div class="tab-pane" id="tab-vendas">

                            <div class="col-md-12 mt-3 table-responsive">
                                <div class="table-responsive-sm">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Data</th>
                                                <th>Valor total</th>
                                                <th>Estado</th>
                                                <th>Chave</th>
                                                <th>Número documento</th>
                                                <th>Tipo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $total = 0; @endphp
                                            @forelse($data as $c)
                                            <tr>
                                                <td>{{ __data_pt($c->created_at) }}</td>
                                                <td>{{ __moeda($c->total) }}</td>
                                                <td>
                                                    @if($c->estado == 'aprovado')
                                                    <span class="btn btn-success text-white btn-sm w-50">Aprovado</span>
                                                    @elseif($c->estado == 'cancelado')
                                                    <span class="btn btn-danger text-white btn-sm w-50">Cancelado</span>
                                                    @elseif($c->estado == 'rejeitado')
                                                    <span class="btn btn-warning text-white btn-sm w-50">Rejeitado</span>
                                                    @else
                                                    <span class="btn btn-info text-white btn-sm w-50">Novo</span>
                                                    @endif
                                                </td>
                                                <td>{{ $c->estado == 'aprovado' ? $c->chave : '--' }}</td>
                                                <td>{{ $c->estado == 'aprovado' ? $c->numero : '--' }}</td>
                                                <td>{{ $c->tipo == 'nfce' ? 'PDV NFCe' : 'NFe' }}</td>
                                            </tr>

                                            @php $total += $c->total; @endphp

                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Nada encontrado</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-dark">
                                                <td class="text-white">Total</td>
                                                <td class="text-white">{{ __moeda($total) }}</td>
                                                <td colspan="4"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--  -->

                    <div class="tab-content b-0 mb-0">
                        <div class="tab-pane" id="tab-produtos">
                            <div class="col-md-12 mt-3 table-responsive">
                                <div class="table-responsive-sm">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th></th>
                                                <th>Produto</th>
                                                <th>Quantidade</th>
                                                <th>Valor unitário</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($produtos as $p)
                                            <tr>
                                                <td><img class="img-60" src="{{ $p->produto->img }}"></td>
                                                <td>{{ $p->produto->nome }}</td>
                                                <td>{{ number_format($p->quantidade, 2) }}</td>
                                                <td>{{ __moeda($p->valor_unitario) }}</td>
                                                <td>{{ __moeda($p->quantidade*$p->valor_unitario) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--  -->

                    <div class="tab-content b-0 mb-0">
                        <div class="tab-pane" id="tab-faturas">
                            <div class="col-md-12 mt-3 table-responsive">
                                <div class="table-responsive-sm">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Descrição</th>
                                                <th>Data de cadastro</th>
                                                <th>Data de vencimento</th>
                                                <th>Data de recebimento</th>
                                                <th>Valor</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($faturas as $c)
                                            <tr>
                                                <td>{{ $c->descricao }}</td>
                                                <td>{{ __data_pt($c->created_at) }}</td>
                                                <td>{{ __data_pt($c->data_vencimento, 0) }}</td>
                                                <td>{{ $c->status ? __data_pt($c->data_recebimento, 0) : '--' }}</td>
                                                <td>{{ __moeda($c->valor_integral) }}</td>
                                                <td>
                                                    @if($c->status)
                                                    <span class="btn btn-success position-relative me-lg-5 btn-sm">
                                                        <i class="ri-checkbox-line"></i> Recebido
                                                    </span>
                                                    @else
                                                    <span class="btn btn-warning position-relative me-lg-5 btn-sm">
                                                        <i class="ri-alert-line"></i> Pendente
                                                    </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="/assets/vendor/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<script src="/assets/js/pages/demo.form-wizard.js"></script>
@endsection
