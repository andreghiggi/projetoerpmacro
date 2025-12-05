@extends('layouts.app', ['title' => 'Home'])
@if(!__isContador())
@section('css')
<link rel="stylesheet" type="text/css" href="/css/home.css">
@endsection
@section('content')
<div class="mt-3">
    <div class="row">

        @if(__isAdmin())
        <div class="card">
            <div class="row m-2">
                @if($msgPlano != "")
                <div class="col-lg-12 mb-2">
                    <p class="text-danger">{{ $msgPlano }}</p>
                    <a href="{{ route('payment.index') }}" class="btn btn-success btn-lg pulse-success">Contratar Plano</a>
                </div>
                @endif

                @if($botaoContrato != "")
                <div class="col-lg-12 mb-2 text-end">
                    <a href="{{ route('assinar-contrato.index') }}" class="btn btn-primary pulse-primary">
                        <i class="ri-quill-pen-line"></i>
                        {{ $botaoContrato }}
                    </a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <h3>Painel</h3>

                @if(in_array('Cards de receita', $homeComponentes) || $configGeral == null)
                <div class="row g-3 align-items-stretch mt-2">

                    <div class="col-12 col-lg-4">
                        <div class="row">
                            <div class="card shadow-sm border-0" style="border-radius: 8px;">
                                <div class="card-header text-white" style="background: #303256; border-radius: 8px;">
                                    <div class="fw-bold fs-5 text-white" style="padding-top: 10px">
                                        Seja Bem Vindo!
                                    </div>
                                    <small class="text-white">Área Administrativa</small>
                                </div>

                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ Auth::user()->img }}" 
                                        alt="foto perfil" 
                                        class="rounded-circle shadow-sm" 
                                        width="64" height="64">

                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ Auth::user()->name }}</div>
                                            @if(__isAdmin())
                                            <div class="text-muted small">Admin</div>
                                            @endif

                                            <div class="d-flex align-items-center justify-content-between mt-3">
                                                <div class="text-muted small">
                                                    <strong class="fs-5 text-dark">{{ $totalDeVendaSemana }}</strong><br>Qtd. de vendas nesta semana
                                                </div>
                                                <div>
                                                    <a href="{{ route('vendas.index', ['start_date='.\Carbon\Carbon::parse($inicioSemana,0)->format('Y-m-d')]) }}" class="btn btn-sm text-white px-3" style="background-color: #303256; border-radius: 8px;">
                                                        Abrir
                                                    </a>
                                                    <div class="text-muted small mt-2">Vendas semanal</div>
                                                    <div class="fw-bold fs-5 text-dark">R$ {{ __moeda($somaSemanal) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card card-caixa">
                                <h5>Caixas Operando</h5>

                                @if(sizeof($caixas) == 0)
                                <div class="box-info">
                                    <i class="ri-checkbox-circle-line"></i>
                                    No momento, não existem caixas operando.
                                </div>
                                <div class="text-center">
                                    <a href="{{ route('caixa.create') }}" class="btn-abrir-caixa">ABRIR CAIXA <i class="ri-folder-open-fill"></i></a>
                                </div>
                                @else
                                <ul class="list-unstyled m-0">
                                    @foreach($caixas as $c)
                                    @if(__isAdmin() || Auth::user()->id == $c->usuario_id)
                                    <a href="{{ route('caixa.show', [$c->id]) }}"><li class="list-item-elev d-flex align-items-center mb-2 p-2">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold text-truncate">{{ $c->usuario->name }}</div>
                                            <div class="small text-muted">Total de vendas: <b>{{ sizeof($c->vendas) + sizeof($c->vendasPdv) }}</b></div>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-success fw-bold">R$ {{ __moeda($c->vendas->sum('total') + $c->vendasPdv->sum('total')) }}</div>
                                            <div class="small text-muted">{{ __data_pt($c->created_at) }}</div>
                                        </div>
                                    </li></a>

                                    @endif
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-8">
                        <div class="row g-3">
                            <div class="col-12 col-xl-4 col-md-6">
                                <div class="card mini-card h-100">
                                    <div class="card-body d-flex align-items-start justify-content-between">
                                        <div>
                                            <div class="text-muted small fw-semibold">Receita Mensal</div>
                                            <div class="value mt-1" id="kpi-rec-liq">R$ {{ __moeda($somaMensal) }}</div>
                                            <a class="small" href="#">Sobre as vendas</a>
                                        </div>
                                        <div class="icon"><i class="bi bi-coin"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-4 col-md-6">
                                <div class="card mini-card h-100">
                                    <div class="card-body d-flex align-items-start justify-content-between">
                                        <div>
                                            <div class="text-muted small fw-semibold">Custo Produto Mensal</div>
                                            <div class="value mt-1" id="kpi-custo">R$ {{ __moeda($custoMensal) }}</div>
                                            <a class="small" href="#">Custo sobre os produtos</a>
                                        </div>
                                        <div class="icon"><i class="bi bi-coin"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-4 col-md-6">
                                <div class="card mini-card h-100">
                                    <div class="card-body d-flex align-items-start justify-content-between">
                                        <div>
                                            <div class="text-muted small fw-semibold">Em Estoque</div>
                                            <div class="value mt-1" id="kpi-estoque">R$ {{ __moeda($totalEmEstoque) }}</div>
                                            <a class="small" href="#">Valor sobre produtos</a>
                                        </div>
                                        <div class="icon"><i class="bi bi-exclamation-circle"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-1">

                            @if(sizeof($produtosMaisVendidosMensal) > 0)
                            <div class="col-xl-6 col-12 col-md-12">
                                <div class="card card-elev">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Produtos Mais Vendidos Mensal</h5>
                                        <ul class="list-unstyled m-0">
                                            @foreach($produtosMaisVendidosMensal as $p)
                                            <li class="list-item-elev d-flex align-items-center mb-2 p-2">
                                                <img class="thumb me-3" src="{{ $p['imagem'] }}" alt="">
                                                <div class="flex-grow-1" style="max-width: 70%">
                                                    <div class="fw-semibold text-truncate">{{ $p['nome'] }}</div>
                                                    <div class="small text-muted">Vendas: {{ $p['total_vendas'] }} (Total: {{ $p['total_itens'] }} itens)</div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-success fw-bold">R$ {{ __moeda($p['sub_total']) }}</div>
                                                    <div class="small text-muted">R$ {{ __moeda($p['valor_unitario']) }} / {{ $p['unidade'] }}</div>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            @endif

                            @if(sizeof($melhoresClientes) > 0)
                            <div class="col-xl-6 col-12 col-md-12">
                                <div class="card card-elev">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Principais Clientes do Mês</h5>
                                        <ul class="list-unstyled m-0">
                                            @foreach($melhoresClientes as $c)
                                            <li class="list-item-elev d-flex align-items-center mb-2 p-2">
                                                <img class="thumb me-3" src="{{ $c['imagem'] }}" alt="">
                                                <div class="flex-grow-1" style="max-width: 70%">
                                                    <div class="fw-semibold text-truncate">{{ $c['razao_social'] }}</div>
                                                    <div class="small text-muted">Total de vendas: {{ $c['total_vendas'] }}</div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-success fw-bold">R$ {{ __moeda($c['total']) }}</div>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
            @endif

            @if(in_array('Cards por período', $homeComponentes) || $configGeral == null)
            <div class="row p-2">

                <div class="range-container">
                    <div class="range-filter">
                        <button class="range-btn active" data-periodo="1">Hoje</button>
                        <button class="range-btn" data-periodo="7">Esta semana</button>
                        <button class="range-btn" data-periodo="30">Este mês</button>
                        <button class="range-btn" data-periodo="365">Este ano</button>
                    </div>
                </div>


                @if(__countLocalAtivo() > 1)
                <div class="col-md-2">
                    {!!Form::select('local_id', 'Local', [
                    '' => 'Todos'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                    ->attrs(['class' => 'form-select'])
                    !!}
                </div>
                @else
                <input id="inp-local_id" type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
                @endif
            </div>
            <div class="row p-2 g-3">
                <div class="col-12 col-lg-3 col-xl-2">
                    <div class="card widget-icon-box text-bg-success">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-uppercase">Contas a Receber</h4>
                                <h3 class="total-receber">R$ 0,00</h3>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title shadow">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-3 col-xl-2">
                    <div class="card widget-icon-box text-bg-danger">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-uppercase">Contas a Pagar</h4>
                                <h3 class="total-pagar">R$ 0,00</h3>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title shadow">
                                    <i class="ri-bank-card-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-3 col-xl-2">
                    <div class="card widget-icon-box text-bg-info">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-uppercase">Produtos</h4>
                                <h3 class="total-produtos">0</h3>
                                @if($emPromocao > 0)
                                <span class="span-box">
                                    Produtos em promoção 
                                    <span class="badge bg-success">{{ $emPromocao }}</span>
                                </span>
                                @endif
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title shadow">
                                    <i class="ri-box-3-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-3 col-xl-2">
                    <div class="card widget-icon-box text-bg-dark">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-uppercase">Clientes</h4>
                                <h3 class="total-clientes">0</h3>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title shadow">
                                    <i class="ri-account-box-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-3 col-xl-2">
                    <div class="card widget-icon-box text-bg-primary">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-uppercase">Total de Vendas</h4>
                                <h3 class="total-vendas">R$ 0,00</h3>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title shadow">
                                    <i class="ri-shopping-cart-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-3 col-xl-2">
                    <div class="card widget-icon-box text-bg-warning">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-uppercase">Total de Compras</h4>
                                <h3 class="total-compras">R$ 0,00</h3>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title shadow">
                                    <i class="ri-shopping-bag-2-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!-- fim cards -->

            @if(in_array('Cards de recebimento', $homeComponentes) || $configGeral == null)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-end filter-row mb-4 g-1">
                        <div class="col-auto">
                            <label for="periodo_inicial" class="mb-0">Período</label>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="date" id="periodo_inicial" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-auto">
                            <label class="mb-0">à</label>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="date" id="periodo_final" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-auto">
                            <button class="btn" type="button" id="btn-filtrar-contas" style="background-color: #303256; border-radius: 8px;">
                                <i class="ri-search-line text-white"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">

                        <div class="col-12 col-lg-4">
                            <div class="card finance-card h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h4 class="text-uppercase mb-2">Saldo Total Atualizado</h4>
                                        <h3 class="saldo-contas fs-4">R$ 0,00</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card finance-card h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h4 class="text-uppercase mb-2">Recebidos</h4>
                                        <h3 class="valor-recebido fs-4">R$ 0,00</h3>
                                        <div class="small">A Receber <strong class="valor-areceber">R$ 0,00</strong></div>
                                    </div>
                                    <a href="{{ route('conta-receber.create') }}" class="btn mt-3 w-100 btn-sm">NOVO RECEBIMENTO</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card finance-card h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h4 class="text-uppercase mb-2">Pagos</h4>
                                        <h3 class="valor-pago fs-4">R$ 0,00</h3>
                                        <div class="small">A Pagar <strong class="valor-apagar">R$ 0,00</strong></div>
                                    </div>
                                    <a href="{{ route('conta-pagar.create') }}" class="btn mt-3 w-100 btn-sm">NOVO PAGAMENTO</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(in_array('Gráfico de vendas', $homeComponentes))
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <h5>Vendas</h5>
                        </div>
                        <div class="card-body">
                            <h5>Total de vendas {{ $mes }} R$ <strong>{{ __moeda($totalVendasMes)}}</strong></h5>
                            <p>Vendas meses anteriores.</p>
                            @foreach($somaVendasMesesAnteriores as $key => $s)
                            <h6>{{ $key }}: <strong class="text-success">R$ {{ __moeda($s) }}</strong></h6>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>Grafico de vendas mensal (valores por dia)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-vendas-mes"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(in_array('Gráfico de compras', $homeComponentes))
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <h5>Compras</h5>
                        </div>
                        <div class="card-body">
                            <h5>Total de compras {{ $mes }} R$ <strong>{{ __moeda($totalComprasMes)}}</strong></h5>
                            <p>Vendas meses anteriores.</p>
                            @foreach($somaComprasMesesAnteriores as $key => $s)
                            <h6>{{ $key }}: <strong class="text-success">R$ {{ __moeda($s) }}</strong></h6>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>Grafico de compras mensal (valores por dia)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-compras-mes"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(in_array('Gráfico de emissão dia', $homeComponentes))
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <h5>Volume</h5>
                        </div>
                        <div class="card-body">
                            <h4>R$ <strong>{{ __moeda($totalEmitidoMes)}}</strong></h4>
                            <p>Notas emitidas neste mês.</p>
                            <h6>Emissões de NFe: <strong class="text-success">{{ $totalNfeCount }}</strong></h6>
                            <h6>Emissões de NFCe: <strong class="text-success">{{ $totalNfceCount }}</strong></h6>
                            <h6>Emissões de CTe: <strong class="text-success">{{ $totalCteCount }}</strong></h6>
                            <h6>Emissões de MDFe: <strong class="text-success">{{ $totalMdfeCount }}</strong></h6>

                        </div>
                    </div>

                    @if($empresa->plano)
                    <div class="card mt-2">
                        <div class="card-header">
                            <h5>Plano</h5>
                        </div>
                        <div class="card-body">
                            <h4>{{ $empresa->plano->plano->nome }}</h4>
                            <h6>Total de emissões NFe: <strong class="text-danger">{{ $empresa->plano->plano->maximo_nfes }}</strong></h6>
                            <h6>Total de emissões NFCe: <strong class="text-danger">{{ $empresa->plano->plano->maximo_nfces }}</strong></h6>
                            <h6>Total de emissões CTe: <strong class="text-danger">{{ $empresa->plano->plano->maximo_ctes }}</strong></h6>
                            <h6>Total de emissões MDFe: <strong class="text-danger">{{ $empresa->plano->plano->maximo_mdfes }}</strong></h6>

                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>Grafico de emissão mensal (valores por dia)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-emissao-mes"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            @endif

            <div class="row">
                @if(in_array('Gráfico de emissão mensal', $homeComponentes))
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Grafico de emissão mensal (quantidade emitida)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-emissao-mes-contador"></canvas>
                        </div>
                    </div>
                    <hr>
                </div>
                @endif
                @if(in_array('Gráfico de emissão acumulado', $homeComponentes))
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Grafico de emissão últimos meses (valor mensal acumulado)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-emissao-ult-meses"></canvas>
                        </div>
                    </div>
                    <hr>
                </div>
                @endif

            </div>

            <div class="row">
                @if(in_array('Gráfico contas a receber', $homeComponentes))
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4">Contas a receber</h4>
                            <div dir="ltr">

                                <canvas id="conta-receber" style="width: 100%" data-colors="#4A4AFD, #B6D7A8, #B6D7A8"></canvas>
                            </div>
                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                    <hr>
                </div><!-- end col-->
                @endif

                @if(in_array('Gráfico contas a pagar', $homeComponentes))
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4">Contas a pagar</h4>
                            <div dir="ltr">

                                <canvas id="conta-pagar" data-colors="#4A4AFD, #B6D7A8, #B6D7A8"></canvas>

                            </div>
                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                    <hr>
                </div><!-- end col-->
                @endif
            </div>
            <div class="row">
                @if(in_array('Gráfico de emissão de CTe', $homeComponentes))
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Grafico de emissão mensal CTe (quantidade emitida)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-emissao-mes-cte"></canvas>
                        </div>
                    </div>
                </div>
                @endif
                @if(in_array('Gráfico de emissão de MDFe', $homeComponentes))
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Grafico de emissão mensal MDFe (quantidade emitida)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="grafico-emissao-mes-mdfe"></canvas>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body">
            <h3>Painel</h3>

            <h5>Olá, <strong class="text-success">{{ get_name_user() }}</strong> seja bem vindo!</h5>

            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('nfe.create') }}" class="btn btn-lg w-100 btn-light">
                        <i class="ri-shopping-bag-line"></i>
                        Nova Venda
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('frontbox.create') }}" class="btn btn-lg w-100 btn-light">
                        <i class="ri-shopping-basket-2-fill"></i>
                        Abrir PDV
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('produtos.create') }}" class="btn btn-lg w-100 btn-light">
                        <i class="ri-price-tag-3-line"></i>
                        Novo Produto
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('clientes.create') }}" class="btn btn-lg w-100 btn-light">
                        <i class="ri-user-add-line"></i>
                        Novo Cliente
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@if(__isAdmin())
@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
    $(function() {
        setTimeout(() => {
            buscaDadosGraficoMes()
            buscaDadosGraficoMesContador()
            buscaDadosUlitmosMeses()
            contaReceber()
            contaPagar()
            buscaDadosGraficoMesCte()
            buscaDadosGraficoMesMdfe()

            dadosCards()
            buscaDadosGraficoVendasMes()
            buscaDadosGraficoComprasMes()
            filtrarContas()
        }, 10)
    })

    $(document).on("click", "#btn-filtrar-contas", function () {
        filtrarContas()
    });

    function filtrarContas(){
        let empresa_id = $('#empresa_id').val()

        $.get(path_url + "api/graficos/cards-contas", {
            empresa_id: empresa_id,
            periodo_inicial: $('#periodo_inicial').val(),
            periodo_final: $('#periodo_final').val(),
        })
        .done((data) => {
            console.log(data)
            let saldo = data.recebidas - data.pagas
            $('.saldo-contas').text("R$ " + convertFloatToMoeda(saldo))

            $('.valor-recebido').text("R$ " + convertFloatToMoeda(data.recebidas))
            $('.valor-areceber').text("R$ " + convertFloatToMoeda(data.aReceber))

            $('.valor-pago').text("R$ " + convertFloatToMoeda(data.pagas))
            $('.valor-apagar').text("R$ " + convertFloatToMoeda(data.aPagar))

            if(saldo > 0){
             $('.saldo-contas').addClass('text-success') 
         }else if(saldo < 0){
             $('.saldo-contas').addClass('text-danger') 
         }
     })
        .fail((err) => {
            console.log(err)
        })
    }

    $(document).on("change", "#inp-periodo", function () {
        dadosCards()
    })

    $(document).on("change", "#inp-local_id", function () {
        dadosCards()
    })

    var periodo = 1

    $('.range-btn').on('click', function () {

        $('.range-btn').removeClass('active');
        $(this).addClass('active');

        periodo = $(this).data('periodo');
        dadosCards()
        console.log("Período selecionado:", periodo);
    });

    function dadosCards(){

        let local_id = $('#inp-local_id').val()
        let empresa_id = $('#empresa_id').val()
        let usuario_id = $('#usuario_id').val()

        $.get(path_url + "api/graficos/dados-cards", {
            empresa_id: empresa_id,
            usuario_id: usuario_id,
            periodo: periodo,
            local_id: local_id
        })
        .done((success) => {

            $('.total-clientes').text(success['clientes'])
            $('.total-produtos').text(success['produtos'])
            $('.total-vendas').text("R$ " + convertFloatToMoeda(success['vendas']))
            $('.total-compras').text("R$ " + convertFloatToMoeda(success['compras']))
            $('.total-receber').text("R$ " + convertFloatToMoeda(success['contas_receber']))
            $('.total-pagar').text("R$ " + convertFloatToMoeda(success['contas_pagar']))
        })
        .fail((err) => {
            console.log(err)
        })
    }

    function buscaDadosGraficoVendasMes() {
        let empresa_id = $('#empresa_id').val()

        $.get(path_url + "api/graficos/grafico-vendas-mes", {
            empresa_id: empresa_id
        })
        .done((success) => {
            iniciaGraficoVendasMes(success)
        })
        .fail((err) => {
            console.log(err)
        })
    }

    function buscaDadosGraficoComprasMes() {
        let empresa_id = $('#empresa_id').val()

        $.get(path_url + "api/graficos/grafico-compras-mes", {
            empresa_id: empresa_id
        })
        .done((success) => {
            iniciaGraficoComprasMes(success)
        })
        .fail((err) => {
            console.log(err)
        })
    }

    function buscaDadosGraficoMes() {
        let empresa_id = $('#empresa_id').val()

        $.get(path_url + "api/graficos/grafico-mes", {
            empresa_id: empresa_id
        })
        .done((success) => {
            iniciaGraficoMes(success)
        })
        .fail((err) => {
            console.log(err)
        })
    }

    function buscaDadosGraficoMesContador() {
        let empresa_id = $('#empresa_id').val()

        $.get(path_url + "api/graficos/grafico-mes-contador", {
            empresa_id: empresa_id
        })
        .done((success) => {
            iniciaGraficoMesContador(success)
        })
        .fail((err) => {
            console.log(err)
        })
    }

    function buscaDadosGraficoMesCte() {
        let empresa_id = $('#empresa_id').val()

        $.get(path_url + "api/graficos/grafico-mes-cte", {
            empresa_id: empresa_id
        })
        .done((success) => {
            iniciaGraficoMesCte(success)
        })
        .fail((err) => {
            console.log(err)
        })
    }

    function buscaDadosGraficoMesMdfe() {
        let empresa_id = $('#empresa_id').val()

        $.get(path_url + "api/graficos/grafico-mes-mdfe", {
            empresa_id: empresa_id
        })
        .done((success) => {
            iniciaGraficoMesMdfe(success)
        })
        .fail((err) => {
            console.log(err)
        })
    }

    function buscaDadosUlitmosMeses() {
        let empresa_id = $('#empresa_id').val()

        $.get(path_url + "api/graficos/grafico-ult-meses", {
            empresa_id: empresa_id
        })
        .done((success) => {
            iniciaGraficoUltMeses(success)
        })
        .fail((err) => {
            console.log(err)
        })
    }

    function contaReceber() {
        let empresa_id = $('#empresa_id').val()

        $.get(path_url + "api/graficos/grafico-conta-receber", {
            empresa_id: empresa_id
        })
        .done((success) => {
            contaReceberTotal(success)
        })
        .fail((err) => {
            console.log(err)
        })
    }

    function contaPagar() {
        let empresa_id = $('#empresa_id').val()

        $.get(path_url + "api/graficos/grafico-conta-pagar", {
            empresa_id: empresa_id
        })
        .done((success) => {
            contaPagarTotal(success)
        })
        .fail((err) => {
            console.log(err)
        })
    }

    function iniciaGraficoVendasMes(data) {
        const ctx = document.getElementById('grafico-vendas-mes');
        if(ctx){
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: montaLabels(data),
                    datasets: [{
                        label: 'total',
                        data: montaValues(data),
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    function iniciaGraficoComprasMes(data) {
        const ctx = document.getElementById('grafico-compras-mes');
        if(ctx){
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: montaLabels(data),
                    datasets: [{
                        label: 'total',
                        data: montaValues(data),
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    function iniciaGraficoMes(data) {
        const ctx = document.getElementById('grafico-emissao-mes');
        if(ctx){
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: montaLabels(data),
                    datasets: [{
                        label: 'emissão',
                        data: montaValues(data),
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    function iniciaGraficoMesContador(data) {
        const ctx = document.getElementById('grafico-emissao-mes-contador');
        if(ctx){
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: montaLabels(data),
                    datasets: [{
                        label: 'emissão',
                        data: montaValues(data),
                        borderWidth: 1,
                        borderColor: '#19AC65',
                        backgroundColor: '#19AC65'
                    }],

                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    function iniciaGraficoMesCte(data) {
        const ctx = document.getElementById('grafico-emissao-mes-cte');
        if(ctx){
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: montaLabels(data),
                    datasets: [{
                        label: 'emissão',
                        data: montaValues(data),
                        borderWidth: 1,
                        borderColor: '#19AC65',
                        backgroundColor: '#19AC65'
                    }],

                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    function iniciaGraficoMesMdfe(data) {
        const ctx = document.getElementById('grafico-emissao-mes-mdfe');
        if(ctx){
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: montaLabels(data),
                    datasets: [{
                        label: 'emissão',
                        data: montaValues(data),
                        borderWidth: 1,
                        borderColor: '#19AC65',
                        backgroundColor: '#19AC65'
                    }],

                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    function iniciaGraficoUltMeses(data) {
        const ctx = document.getElementById('grafico-emissao-ult-meses');
        if(ctx){
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: montaLabels(data),
                    datasets: [{
                        label: 'emissão',
                        data: montaValues(data),
                        borderWidth: 1,
                        borderColor: '#FF6384',
                        backgroundColor: '#FF6384'
                    }],

                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    function montaLabels(data) {
        let temp = []
        data.map((x) => {
            temp.push(x.dia)
        })
        return temp
    }

    function montaValues(data) {
        let temp = []
        data.map((x) => {
            temp.push(x.valor)
        })
        return temp
    }

    function montaValuesPendente(data) {
        let temp = []
        data.map((x) => {
            temp.push(x.valorPendente)
        })
        return temp
    }

    function montaValuesQuitado(data) {
        let temp = []
        data.map((x) => {
            temp.push(x.valorQuitado)
        })
        return temp
    }

    function contaReceberTotal(data) {
        var chartElement = document.getElementById('conta-receber');
        if(chartElement){
            var dataColors = chartElement.getAttribute('data-colors');
            var colors = dataColors ? dataColors.split(",") : this.defaultColors
            var ctx = chartElement.getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: montaLabels(data),
                    datasets: [{
                        label: 'Valor a Receber',
                        data: montaValuesPendente(data),
                        fill: '-1',
                        backgroundColor: '#000000',
                    }, 
                    {
                        label: 'Valor Recebido',
                        data: montaValuesQuitado(data),
                        fill: '-1',
                        backgroundColor: '#6AA84F', 
                    }, 
                    {
                        label: 'Total',
                        data: montaValues(data),
                        fill: '0',
                        backgroundColor: '#1261A9',
                    }]
                }, 
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        filler: {
                            propagate: true
                        }, 
                    }, 
                    interaction: {
                        intersect: true, 
                    }
                }
            });
        }
    }

    function contaPagarTotal(data) {
        var chartElement = document.getElementById('conta-pagar');
        if(chartElement){
            var dataColors = chartElement.getAttribute('data-colors');
            var colors = dataColors ? dataColors.split(",") : this.defaultColors
            var ctx = chartElement.getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line', 
                data: {
                    labels: montaLabels(data), 
                    datasets: [{
                        label: 'Valor a Pagar', 
                        data: montaValuesPendente(data),
                        fill: '-1', 
                        backgroundColor: '#000000', 
                    }, 
                    {
                        label: 'Valor Pago', 
                        data: montaValuesQuitado(data),
                        fill: '-1',
                        backgroundColor: '#6AA84F', 
                    }, 
                    {
                        label: 'Total', 
                        data: montaValues(data),
                        fill: '0',
                        backgroundColor: '#1261A9',
                    }]
                }, 
                options: {
                    responsive: true, 
                    maintainAspectRatio: true, 
                    plugins: {
                        filler: {
                            propagate: true
                        }, 
                    }, 
                    interaction: {
                        intersect: true, 
                    }

                }, 
            });
        }
    }
</script>
@endsection
@endif
@else

@include('contador.home')
@endif
