@extends('layouts.app', ['title' => 'Caixa'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            @if($item->status == 0)
            <a href="{{ route('caixa.create') }}" class="btn btn-success">
                <i class="ri-add-circle-fill"></i>
                Abrir Caixa
            </a>
            @else
            <div class="card-body">
                <div class="card-body mt-1">
                    <div class="bg-light-subtle border-top border-bottom border-light">
                        <div class="row text-center">

                            @if(__countLocalAtivo() > 1)
                            <h5 class="mt-2">Local: <strong class="text-danger">{{ $item->localizacao ? $item->localizacao->descricao : '' }}</strong></h5>
                            @endif
                            <div class="col">
                                <p class="text-muted mt-3"><i class="ri-shield-user-fill"></i> Usuário</p>
                                <h3 class="fw-normal mb-3">
                                    <span>{{ $item->usuario->name }}</span>
                                </h3>
                            </div>
                            <div class="col">
                                <p class="text-muted mt-3"><i class="ri-file-list-2-line"></i> Data de Abertura</p>
                                <h3 class="fw-normal mb-3">
                                    <span>{{ __data_pt($item->created_at, 1) }}</span>
                                </h3>
                            </div>
                            <div class="col">
                                <p class="text-muted mt-3"><i class="ri-money-dollar-circle-line"></i> Valor de Abertura</p>
                                <h3 class="fw-normal mb-3">
                                    <span>{{ __moeda($item->valor_abertura) }}</span>
                                </h3>
                            </div>
                            @if($item->contaEmpresa)
                            <div class="col">
                                <p class="text-muted mt-3"><i class="ri-money-dollar-circle-line"></i> Conta</p>
                                <h3 class="fw-normal mb-3">
                                    <span>{{ $item->contaEmpresa->nome }}</span>
                                </h3>
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
                </div>
                
                <div class="row mt-3">
                    <h3 class="text-center">Total por Tipo de Pagamento Vendas:</h3>
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
                    
                    @endif
                    @endforeach
                    <div class="row text-center mt-4">
                        <div class="col-md-4">
                            <div class="card">
                                <h3>Total de vendas: <strong class="text-danger">R${{ __moeda($totalVendas) }}</strong></h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <h3>Total de compras: <strong class="text-danger">R${{ __moeda($totalCompras) }}</strong></h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <h3>Venda de produtos: <strong class="text-danger">R${{ __moeda($totalVendas-$somaServicos) }}</strong></h3>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <h3>Venda de serviços: <strong class="text-danger">R${{ __moeda($somaServicos) }}</strong></h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <h3>Total de registros: <strong class="text-danger">{{ sizeof($data) }}</strong></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="text-center mt-3">Movimentações do caixa</h3>
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
                                @php 
                                $somaReceita = 0; 
                                $somaDespesa = 0; 
                                @endphp
                                @forelse ($data as $i)
                                <tr>
                                    <td>{{ $i->tipo }}</td>
                                    <td>{{ __data_pt($i->created_at, 1) }}</td>
                                    @if($i->tipo != 'OS')
                                    <td>
                                        <strong>R$ {{ __moeda($i->total) }}</strong>
                                        <br>
                                        @if($i->receita == 1)
                                        <strong class="text-success">Receita</strong>
                                        @else
                                        <strong class="text-danger">Despesa</strong>
                                        @endif
                                    </td>
                                    @else
                                    <td>
                                        <strong>R$ {{ __moeda($i->valor) }}</strong>
                                        <br>
                                        @if($i->receita == 1)
                                        <strong class="text-success">Receita</strong>
                                        @else
                                        <strong class="text-danger">Despesa</strong>
                                        @endif
                                    </td>

                                    @endif
                                </tr>
                                @php
                                if($i->receita == 1 && $i->tipo != 'OS'){
                                    $somaReceita += $i->total;
                                }elseif($i->receita == 1 && $i->tipo == 'OS'){
                                    $somaReceita += $i->valor;
                                }else{
                                    $somaDespesa += $i->total;
                                }
                                @endphp
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
                    <div class="col-12 col-md-3">
                        <div class="card card-custom gutter-b bg-light-info">
                            <div class="card-body">
                                <h4 class="card-title">Total Receita: <strong class="text-success">R$ {{ __moeda($somaReceita) }}</strong></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-custom gutter-b bg-light-info">
                            <div class="card-body">
                                <h4 class="card-title">Total Despesa: <strong class="text-danger">R$ {{ __moeda($somaDespesa) }}</strong></h4>
                            </div>
                        </div>
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
                                @forelse ($contas as $i)
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
                                <h3 class="card-title">Total Recebido:</h3>
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
                                <h3 class="card-title">Total Pago:</h3>
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
                            <div class="card-header">
                                <h4 class="card-title">Suprimentos</h4>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead class="table-info">
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

                                            @php
                                            $somaSuprimento += $s->valor;
                                            @endphp
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
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card card-custom gutter-b bg-light-danger">

                            <div class="card-header">
                                <h4 class="card-title">Sangrias</h4>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead class="table-danger">
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

                                            @php
                                            $somaSangria += $s->valor;
                                            @endphp
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
                    </div>
                </div>
            </div>
            <div class="row m-3">
                <div class="col-md-4">
                    <h3 >Total Entrada: 
                        <strong class="text-primary">R$ {{ __moeda($totalVendas + $somaSuprimento + $receber->sum('valor_integral')) }}</strong>
                    </h3>
                </div>
                <div class="col-md-4">
                    <h3>Total Saída: 
                        <strong class="text-primary">R$ {{ __moeda($somaSangria + $pagar->sum('valor_integral')) }}</strong>
                    </h3>
                </div>
                <div class="col-md-4">
                    <h3>Saldo: <strong class="text-success">R$ {{ __moeda($totalVendas + $somaSuprimento + $valor_abertura + $receber->sum('valor_integral') - $somaSangria - $pagar->sum('valor_integral')) }}</strong></h3>
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
            @endif
        </div>
    </div>
</div>

@include('modals._fechamento_caixa', ['not_submit' => true])

@endsection
