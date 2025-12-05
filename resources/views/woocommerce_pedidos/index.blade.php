@extends('layouts.app', ['title' => 'Pedidos Woocommerce'])
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
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('woocommerce-pedidos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>

                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Valor total</th>
                                    <th>Desconto</th>
                                    <th>Valor frete</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>

                                    
                                    <td>{{ $item->pedido_id }}</td>
                                    <td>{{ $item->cliente->info }}</td>
                                    <td>{{ __data_pt($item->data) }}</td>
                                    <td>{{ __moeda($item->total) }}</td>
                                    <td>{{ __moeda($item->desconto) }}</td>
                                    <td>{{ __moeda($item->valor_frete) }}</td>

                                    <td>
                                        <form action="{{ route('woocommerce-pedidos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a class="btn btn-dark btn-sm text-white" href="{{ route('woocommerce-pedidos.show', [$item->id]) }}">
                                                <i class="ri-eye-line"></i>
                                            </a>
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

            </div>
        </div>
    </div>
</div>
@endsection

