@extends('layouts.app', ['title' => 'Detalhes da Troca'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Detalhes da Troca</h4>
                <div style="text-align: right; margin-top: -35px;">
                    <a href="{{ route('nfce.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                <hr class="mt-3">
                <div class="">
                    <h4>Cliente: <strong style="color: steelblue">{{ $item->nfce->cliente_id ? $item->nfce->cliente->razao_social : 'Consumidor Final'}}</strong></h4>
                    <h4>Valor da venda: <strong class="text-success">R$ {{ __moeda($item->valor_original) }}</strong></h4>
                    <h4>Valor da troca: <strong class="text-success">R$ {{ __moeda($item->valor_troca) }}</strong></h4>

                    <h4>Data de cadastro: <strong class="text-success">{{ __data_pt($item->created_at) }}</strong></h4>
                </div>
                <hr>
                <div class="col-lg-12 mt-4">
                    <h5>Itens da Venda</h5>
                    <div class="table-responsive-sm">
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
                                @forelse($item->nfce->itens as $i)
                                <tr>
                                    <td>{{ $i->produto->nome }}</td>
                                    <td>{{ $i->quantidade }}</td>
                                    <td>{{ __moeda($i->valor_unitario) }}</td>
                                    <td>{{ __moeda($i->sub_total) }}</td>
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
                <hr>
                <div class="col-lg-12 mt-4">
                    <h5>Itens Alterados</h5>
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($item->itens as $i)
                                <tr>
                                    <td>{{ $i->produto->nome }}</td>
                                    <td>{{ $i->quantidade }}</td>
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

