@extends('layouts.app', ['title' => 'Dashboard Financeiro'])

@section('css')
<style type="text/css">
    .card {
        border-radius: 12px;
    }
    .card h3 {
        font-weight: 700;
        margin: 0;
    }
    .card h6 {
        font-size: 0.85rem;
    }

</style>
@endsection
@section('content')

<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="text-muted">Pagar Hoje</h6>
                                <h3 class="text-warning">R$ {{ __moeda($dados['pagarHoje']) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="text-muted">Pagar no Mês</h6>
                                <h3 class="text-muted">R$ {{ __moeda($dados['pagarMes']) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="text-muted">Receber Hoje</h6>
                                <h3 class="text-success">R$ {{ __moeda($dados['receberHoje']) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="text-muted">Receber no Mês</h6>
                                <h3 class="text-success">R$ {{ __moeda($dados['receberMes']) }}</h3>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row ">

                    <div class="col-md-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="text-muted">Pagar Vencidas</h6>
                                <h3 class="text-danger">R$ {{ __moeda($dados['pagarVencidas']) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="text-muted">Receber Vencidas</h6>
                                <h3 class="text-info">R$ {{ __moeda($dados['receberVencidas']) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="text-muted">Recebimentos Parciais</h6>
                                <h3 class="text-info">R$ {{ __moeda($dados['recebimentosParciais']) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="text-muted">Saldo do Mês</h6>
                                <h3 class="{{ $dados['saldoMes'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ __moeda($dados['saldoMes']) }}
                                </h3>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row mt-4">
                    <div class="col-md-4 mb-1">
                        <div class="card shadow-sm border-0">
                            <div class="card-header text-center">
                                <h5 class="mb-0">Despesas por Categoria Mensal</h5>
                            </div>
                            <div class="card-body">
                                <div style="height: 360px; display: flex; align-items: center; justify-content: center;">
                                    <canvas 
                                    id="graficoPagarCategoria"  
                                    data-labels='@json(array_column($dadosPagar, "categoria"))'
                                    data-valores='@json(array_column($dadosPagar, "valor"))'
                                    ></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-1">
                        <div class="card shadow-sm border-0">
                            <div class="card-header text-center">
                                <h5 class="mb-0">Receitas por Categoria Mensal</h5>
                            </div>
                            <div class="card-body">
                                <div style="height: 360px; display: flex; align-items: center; justify-content: center;">
                                    <canvas 
                                    id="graficoReceberCategoria"
                                    data-labels='@json(array_column($dadosReceber, "categoria"))'
                                    data-valores='@json(array_column($dadosReceber, "valor"))'
                                    ></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-1">
                        <div class="card shadow-sm border-0">
                            <div class="card-header text-center">
                                <h5 class="mb-0">Lucro dos Últimos 6 Meses</h5>
                            </div>
                            <div class="card-body">
                                <div style="height: 360px; display: flex; align-items: center; justify-content: center;">
                                    <canvas 
                                    id="graficoLucro"
                                    data-labels='@json($lucroGrafico["labels"])'
                                    data-valores='@json($lucroGrafico["lucros"])'
                                    ></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header text-center">
                                <h5 class="mb-0">Fluxo Financeiro dos Últimos 12 Meses</h5>
                            </div>
                            <div class="card-body">
                                <canvas 
                                id="graficoFluxo"
                                height="380"
                                data-labels='@json($grafico["labels"])'
                                data-pagar='@json($grafico["pagar"])'
                                data-receber='@json($grafico["receber"])'
                                ></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="mb-3">Insights Inteligentes</h5>

                        <ul>
                            @foreach($insights as $i)
                            <li class="mb-2">{!! $i !!}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-2">
                    <div class="card-header text-center d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Clientes Inadimplentes</h5>

                        <span class="badge bg-danger text-white px-3 py-2">
                            {{ $inadimplentes['totalInadimplentes'] }} clientes
                        </span>
                    </div>

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-danger mb-0">
                                R$ {{ __moeda($inadimplentes['valorTotalInadimplentes']) }}
                            </h4>
                            <span class="text-muted small">Total em atraso</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cliente</th>
                                        <th class="text-center">Títulos</th>
                                        <th class="text-center">Dias atraso</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inadimplentes['inadimplentes'] as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item['cliente']->razao_social }}</strong><br>
                                            <small class="text-muted">{{ $item['cliente']->cpf_cnpj }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark">
                                                {{ $item['qtd_titulos'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">
                                                {{ $item['dias_atraso'] }} dias
                                            </span>
                                        </td>
                                        <td class="text-end text-danger fw-bold">
                                            R$ {{ __moeda($item['total_vencido']) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            Nenhum cliente inadimplente.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-2">
                    <div class="card-header text-center d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Fornecedores em Atraso</h5>

                        <span class="badge bg-danger text-white px-3 py-2">
                            {{ $dadosPagarVencidos['totalFornecedoresVencidos'] }} fornecedores
                        </span>
                    </div>

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-danger mb-0">
                                R$ {{ __moeda($dadosPagarVencidos['valorTotalPagarVencido']) }}
                            </h4>
                            <span class="text-muted small">Total em atraso</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fornecedor</th>
                                        <th class="text-center">Títulos</th>
                                        <th class="text-center">Dias atraso</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dadosPagarVencidos['pagarVencidos'] as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item['fornecedor']->razao_social }}</strong><br>
                                            <small class="text-muted">{{ $item['fornecedor']->cpf_cnpj }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark">
                                                {{ $item['qtd_titulos'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">
                                                {{ $item['dias_atraso'] }} dias
                                            </span>
                                        </td>
                                        <td class="text-end text-danger fw-bold">
                                            R$ {{ __moeda($item['total_vencido']) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            Nenhum fornecedor em atraso.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <!--  -->
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header text-center d-flex justify-content-between">
                        <h5 class="mb-0">Previsão Financeira</h5>
                        <p>Como base os últimos 6 meses</p>
                    </div>

                    <div class="card-body">

                        <div class="row text-center">

                            <div class="col-md-4 mb-3">
                                <h6 class="text-muted">Receita Prevista</h6>
                                <h3 class="text-success">R$ {{ __moeda($previsoes['previsao_receita']) }}</h3>
                            </div>

                            <div class="col-md-4 mb-3">
                                <h6 class="text-muted">Despesa Prevista</h6>
                                <h3 class="text-danger">R$ {{ __moeda($previsoes['previsao_despesa']) }}</h3>
                            </div>

                            <div class="col-md-4 mb-3">
                                <h6 class="text-muted">Lucro Previsto</h6>
                                <h3 class="{{ $previsoes['previsao_lucro'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ __moeda($previsoes['previsao_lucro']) }}
                                </h3>
                            </div>

                        </div>

                        <hr>

                        <div class="text-center">
                            @php $t = $previsoes['tendencia_lucro']; @endphp

                            @if($t > 0)
                            <span class="badge bg-success">
                                Tendência de alta: {{ number_format($t, 1, ',', '.') }}%
                            </span>
                            @elseif($t < 0)
                            <span class="badge bg-danger">
                                Tendência de baixa: {{ number_format(abs($t), 1, ',', '.') }}%
                            </span>
                            @else
                            <span class="badge bg-secondary">Tendência estável</span>
                            @endif
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>


</div>

@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="/js/dashboard_financeiro.js"></script>

@endsection
