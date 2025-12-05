@extends('layouts.app', ['title' => 'Pedidos Mercado Livre'])
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

                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('cliente_nome', 'Cliente')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('mercado-livre-pedidos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Valor total do pedido</th>
                                    <th>Valor de entrega</th>
                                    <th>Total de itens</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    
                                    <td>{{ $item->_id }}</td>
                                    <td>{{ $item->cliente_nome }} {{ $item->cliente_documento }}</td>
                                    <td>{{ __data_pt($item->data_pedido) }}</td>
                                    <td>{{ __moeda($item->total) }}</td>
                                    <td>{{ __moeda($item->valor_entrega) }}</td>
                                    <td>{{ sizeof($item->itens) }}</td>
                                    <td>
                                        <form action="{{ route('mercado-livre-pedidos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a title="Ver pedido" class="btn btn-dark btn-sm text-white" href="{{ route('mercado-livre-pedidos.show', [$item->id]) }}">
                                                <i class="ri-clipboard-line"></i>
                                            </a>
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
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

