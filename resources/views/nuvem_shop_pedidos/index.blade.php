@extends('layouts.app', ['title' => 'Pedidos Nuvem Shop'])
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
                            {!!Form::text('cliente', 'Cliente')
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
                        
                        <div class="col-lg-4 col-12">
                            <br>

                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('nuvem-shop-pedidos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ações</th>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Valor Total</th>
                                    <th>Valor Frete</th>
                                    <th>Desconto</th>
                                    <th>Status de envio</th>
                                    <th>Status de pagamento</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pedidos as $item)
                                <tr>

                                    <td>
                                        <form action="{{ route('nuvem-shop-pedidos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <!-- <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button> -->

                                            <a class="btn btn-dark btn-sm text-white" href="{{ route('nuvem-shop-pedidos.show', [$item->id]) }}">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </form>
                                    </td>

                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->customer->name }}</td>
                                    <td>{{ __moeda($item->total) }}</td>
                                    <td>{{ __moeda($item->shipping_cost_customer) }}</td>
                                    <td>{{ __moeda($item->discount) }}</td>
                                    <td>{{ $item->shipping_status }}</td>
                                    <td>{{ $item->payment_status }}</td>
                                    <td>{{ __data_pt($item->created_at) }}</td>
                                    
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <br>
                        
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

