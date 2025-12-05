@extends('layouts.app', ['title' => 'Atendimento Garçom'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('funcionario_id', 'Garçom')
                            ->options($funcionario ? [$funcionario->id => $funcionario->nome] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('atendimento-garcom.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-12 mt-3">
                    <span>Total de registros: <strong>{{ $data->total() }}</strong></span>
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Garçom</th>
                                    <th>Data Registro</th>
                                    <th>Comanda</th>
                                    <th>Produto</th>
                                    <th>Valor</th>
                                    <th>Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Garçom">{{ $item->funcionario ? $item->funcionario->nome : '' }}</td>
                                    <td data-label="Data Registro">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Comanda">{{ $item->pedido->comanda }}</td>
                                    <td data-label="Produto">{{ $item->produto->nome }}</td>
                                    <td data-label="Valor">{{ __moeda($item->sub_total) }}</td>
                                    <td data-label="Quantidade">
                                        @if($item->produto->unidade == 'UN')
                                        {{ number_format($item->quantidade, 0) }}
                                        @else
                                        {{ $item->quantidade }}
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <td colspan="4">Total</td>
                                    <td colspan="2">{{ __moeda($data->sum('sub_total')) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
                <br>
                {!! $data->appends(request()->all())->links() !!}
                <br>
                <h4>Total: <strong class="text-success">R$ {{ __moeda($soma) }}</strong></h4>
            </div>
        </div>
    </div>
</div>
@endsection