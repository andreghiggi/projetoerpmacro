@extends('layouts.app', ['title' => 'CashBack Clientes'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <hr class="mt-3">
                <h5>Registros: <strong class="text-primary">{{ $item->razao_social }}</strong></h5>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Data</th>
                                    <th>Valor do crédito</th>
                                    <th>Percentual</th>
                                    <th>Valor da venda</th>
                                    <th>Data de expiração</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($item->cashBacks as $c)
                                <tr>
                                    <td>{{ __data_pt($c->created_at, 1) }}</td>
                                    <td>{{ __moeda($c->valor_credito) }}</td>
                                    <td>{{ __moeda($c->valor_percentual) }}</td>
                                    <td>{{ __moeda($c->valor_venda) }}</td>

                                    <td>{{ __data_pt($c->data_expiracao, 0) }}</td>
                                    <td>
                                        @if($c->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-success">
                                    <td class="text-white">Total</td>
                                    <td class="text-white">{{ __moeda($item->cashBacks->sum('valor_credito')) }}</td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
