@extends('layouts.app', ['title' => 'Gestão de Produção #' .$item->numero_sequencial])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Gestão de Produção <strong class="text-success">#{{ $item->numero_sequencial }}</strong></h4>

        <h5><strong class="text-primary">{{ $item->produto->nome }}</strong></h5>
        <h5>Quantidade: <strong class="text-primary">{{ number_format($item->quantidade, 0) }}</strong></h5>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('gestao-producao.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>

    @php $controleEstoque = 0; @endphp
    <div class="card-body">
        <div class="row">
            <div class="table-responsive">
                <h5>Produtos</h5>
                <table class="table table-dynamic table-produtos">
                    <thead class="table-dark">
                        <tr>
                            <th class="sticky-col first-col">Produto</th>
                            <th>Quantidade</th>
                            <th>Valor Unit.</th>
                            <th>Subtotal</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->produtos as $p)
                        <tr>
                            <td>
                                {{ $p->produto->nome }}

                                @if($p->produto->gerenciar_estoque == 1)
                                @if(!$p->produto->estoque || $p->produto->estoque->quantidade < $p->quantidade)
                                <p class="text-danger" style="font-size: 11px;">Estoque insuficiente</p>
                                @php $controleEstoque = 1; @endphp

                                @endif
                                @endif
                            </td>
                            <td>{{ !$p->produto->unidadeDecimal() ? number_format($p->quantidade, 0, '.', '') : number_format($p->quantidade, 3, '.', '') }}</td>
                            <td>{{ __moeda($p->valor_unitario) }}</td>
                            <td>{{ __moeda($p->sub_total) }}</td>
                            <td>{{ $p->observacao }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Soma</th>
                            <th colspan="2">{{ __moeda($item->produtos->sum('sub_total')) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="table-responsive">
                <h5>Serviços</h5>
                <table class="table table-dynamic table-produtos">
                    <thead class="table-dark">
                        <tr>
                            <th class="sticky-col first-col">Serviço</th>
                            <th>Quantidade</th>
                            <th>Valor Unit.</th>
                            <th>Subtotal</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->servicos as $p)
                        <tr>
                            <td>{{ $p->servico->nome }}</td>
                            <td>{{ number_format($p->quantidade, 0) }}</td>
                            <td>{{ __moeda($p->valor_unitario) }}</td>
                            <td>{{ __moeda($p->sub_total) }}</td>
                            <td>{{ $p->observacao }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Soma</th>
                            <th colspan="2">{{ __moeda($item->servicos->sum('sub_total')) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="table-responsive">
                <h5>Outros Custos</h5>
                <table class="table table-dynamic table-produtos">
                    <thead class="table-dark">
                        <tr>
                            <th class="sticky-col first-col">Serviço</th>
                            <th>Quantidade</th>
                            <th>Valor Unit.</th>
                            <th>Subtotal</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->outros as $p)
                        <tr>
                            <td>{{ $p->descricao }}</td>
                            <td>{{ number_format($p->quantidade, 0) }}</td>
                            <td>{{ __moeda($p->valor_unitario) }}</td>
                            <td>{{ __moeda($p->sub_total) }}</td>
                            <td>{{ $p->observacao }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Soma</th>
                            <th colspan="2">{{ __moeda($item->outros->sum('sub_total')) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>


        <div class="row">
            <div class="col-md-3 text-center">
                <h4>Total Custo <strong class="text-success">R$ {{ __moeda($item->total_final) }}</strong></h4>
            </div>
            <div class="col-md-3 text-center">
                <h4>Frete <strong>R$ {{ __moeda($item->frete) }}</strong></h4>
            </div>

            <div class="col-md-3 text-center">
                <h4>Desconto <strong>R$ {{ __moeda($item->desconto) }}</strong></h4>
            </div>

            <div class="col-md-3 text-center">
                <h4>Custo por item <strong class="text-primary">R$ {{ __moeda($item->total_final/$item->quantidade) }}</strong></h4>
            </div>

            <div class="col-md-9 text-end"></div>
            <div class="col-md-3 text-end">
                <div class="card">
                    <div class="card-body">
                        <h4>Valor de compra atual <strong class="text-danger">R$ {{ __moeda($item->produto->valor_compra) }}</strong></h4>
                        <h4>Valor de venda atual <strong class="text-primary">R$ {{ __moeda($item->produto->valor_unitario) }}</strong></h4>
                    </div>
                </div>
            </div>


        </div>
    </div>

    @if($item->status == 0)
    {!!Form::open()
    ->put()
    ->route('gestao-producao.finish', [$item->id])
    !!}

    <div class="col-12 text-end">
        <button @if($controleEstoque == 1) disabled @endif class="btn btn-success btn-finalizar px-5 m-3">Alterar para Finalizado</button>
    </div>
    {!!Form::close()!!}
    @endif
</div>

@endsection

