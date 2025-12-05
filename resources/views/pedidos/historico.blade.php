@extends('layouts.app', ['title' => 'Histórico de Comandas'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-2">
                            {!!Form::text('comanda', 'Pesquisar por comanda')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('pedidos-cardapio.historico') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Comanda</th>
                                    <th>Mesa</th>
                                    <th>Cliente</th>
                                    <th>Data de abertura</th>
                                    <th>Data de fechamento</th>
                                    <th>Total de itens</th>
                                    <th>Valor total</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Comanda">#{{ $item->comanda }}</td>
                                    <td data-label="Mesa">{{ $item->_mesa ? $item->_mesa->nome : '' }}</td>
                                    <td data-label="Cliente">{{ $item->cliente_nome }}</td>
                                    <td data-label="Data de abertura">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Data de fechamento">{{ __data_pt($item->data_fechamento) }}</td>
                                    <td data-label="Total de itens">{{ sizeof($item->itens) }}</td>
                                    <td data-label="Valor total">{{ __moeda($item->total) }}</td>
                                    <td>
                                        <a href="{{ route('pedidos-cardapio.show', [$item->id]) }}" class="btn btn-sm btn-dark">Ver</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <br>
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection