@extends('layouts.app', ['title' => 'Fechar Caixa'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                @php
                $soma = 0;
                @endphp
                <div class="row mt-3">
                    <p class="text-muted">Total por Tipo de Pagamento</p>
                    @foreach($somaTiposPagamento as $key => $tp)
                    @if($tp > 0)
                    <div class="col-sm-4 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="">
                                    {{App\Models\Nfce::getTipoPagamento($key)}}
                                </h3>
                            </div>
                            @php
                            if($key == '01') $somaDinheiro = $tp;
                            @endphp
                            <div class="card-body">
                                <h4 class="text-success">R$ {{ __moeda($tp) }}</h4>
                            </div>
                        </div>
                    </div>
                    @php
                    $soma += $tp
                    @endphp
                    @endif
                    @endforeach
                    <div class="row text-center mt-4">
                        <div class="col-md-4 card">
                            <h3>Total de vendas: <strong class="text-danger">{{ __moeda($soma) }}</strong></h3>
                        </div>
                        <div class="col-md-4 card">
                            <h3>Venda de produtos: <strong class="text-danger">{{ __moeda($soma-$somaServicos) }}</strong></h3>
                        </div>

                        <div class="col-md-4 card">
                            <h3>Venda de serviços: <strong class="text-danger">{{ __moeda($somaServicos) }}</strong></h3>
                        </div>
                    </div>
                </div>
                <h3 class="text-center mt-3">Movimentações de Vendas</h3>
                <div class="col-md-12 mt-4 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $i)
                                <tr>
                                    <td>{{ $i->tipo }}</td>
                                    <td>{{ __data_pt($i->created_at, 0) }}</td>
                                    <td>{{ __moeda($i->total) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Nenhum registro</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <h3 class="text-center mt-5">Movimentações de Recebimentos</h3>
                <div class="col-md-12 mt-4 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contas as $i)
                                <tr>
                                    <td>{{ $i->tipo }}</td>
                                    <td>{{ __data_pt($i->created_at, 0) }}</td>
                                    <td>{{ __moeda($i->valor_integral) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Nenhum registro</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 col-xl-6">
                        <div class="card card-custom gutter-b bg-light-info">
                            <div class="card-body">
                                <h2 class="card-title">Total Recebido:</h2>
                                @if(sizeof($receber) > 0)
                                <h4>Valor: R$ {{ __moeda($receber->sum('valor_integral')) }}</h4>
                                @else
                                <h4>R$ 0,00</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card card-custom gutter-b bg-light-danger">
                            <div class="card-body">
                                <h2 class="card-title">Total Pago:</h2>
                                @if(sizeof($pagar) > 0)
                                <h4>Valor: R$ {{ __moeda($pagar->sum('valor_integral')) }}</h4>
                                @else
                                <h4>R$ 0,00</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @php
                $somaSuprimento = 0;
                $somaSangria = 0;
                @endphp
                <div class="row mt-3">
                    <div class="col-12 col-xl-6">
                        <div class="card card-custom gutter-b bg-light-info">
                            <div class="card-body">
                                <h2 class="card-title">Suprimentos:</h2>
                                @if(sizeof($suprimentos) > 0)
                                @foreach($suprimentos as $s)
                                <h4>Valor: R$ {{number_format($s->valor, 2, ',', '.')}}</h4>
                                @php
                                $somaSuprimento += $s->valor;
                                @endphp
                                @endforeach
                                @else
                                <h4>R$ 0,00</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card card-custom gutter-b bg-light-danger">
                            <div class="card-body">
                                <h2 class="card-title">Sangrias:</h2>
                                @if(sizeof($sangrias) > 0)
                                @foreach($sangrias as $s)
                                <h4>Valor: R$ {{number_format($s->valor, 2, ',', '.')}}</h4>
                                @php
                                $somaSangria += $s->valor;
                                @endphp
                                @endforeach
                                @else
                                <h4>R$ 0,00</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row m-3">
                        <div class="col-12 col-md-6">
                            <h4>Total Entrada: <strong class="text-primary">R$ {{ __moeda($soma + $somaSuprimento + $receber->sum('valor_integral')) }}</strong> </h4>
                            <h4>Total Saída: <strong class="text-primary">R$ {{ __moeda($somaSangria + $pagar->sum('valor_integral')) }}</strong></h4>
                            <h3>Valor Fechamento: <strong class="text-primary">R$ {{ __moeda($item->valor_fechamento) }}</strong></h3>
                        </div>

                        <div class="col-12 col-md-6">

                            <h4>Valor em Dinheiro: <strong class="text-danger">R$ {{ __moeda($item->valor_dinheiro) }}</strong></h4>
                            <h4>Valor em Cheque: <strong class="text-danger">R$ {{ __moeda($item->valor_cheque) }}</strong></h4>
                            <h4>Valor Outros: <strong class="text-danger">R$ {{ __moeda($item->valor_outros) }}</strong></h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 m-3">
                    @if(sizeof($data) == 0)
                    <h3>Caixa sem movimentação!</h3>
                    @else

                    @if(sizeof($contasEmpresa) == 0)
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#fechamento_caixa">
                        <i class="ri-add-circle-fill"></i>
                        Fechar Caixa
                    </button>
                    @else
                    <a class="btn btn-danger" href="{{ route('caixa.fechar-conta', [$item->id]) }}">
                        <i class="ri-add-circle-fill"></i>
                        Fechar Caixa
                    </a>
                    @endif

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('modals._fechamento_caixa', ['not_submit' => true])
@endsection
