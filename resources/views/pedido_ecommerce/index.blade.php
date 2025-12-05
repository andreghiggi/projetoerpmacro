@extends('layouts.app', ['title' => 'Pedidos de Ecommerce'])
@section('css')
<style type="text/css">
    .card-title strong{
        color: #159488;
    }

    h4 strong{
        color: #4254BA;
    }
</style>
@endsection
@section('content')

<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">

                @foreach($pagamentosAlterados as $p)
                <label class="badge @if($p['status'] == 'approved') bg-success @else bg-danger @endif p-2">#{{ $p['hash_pedido']}} - {{ $p['status'] }}</label>
                @endforeach
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('cliente_delivery_id', 'Pesquisar por cliente')
                            ->options($cliente != null ? [$cliente->id => ($cliente->razao_social . " - " . $cliente->telefone)] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('estado', 'Estado', ['' => 'Selecione'] + App\Models\PedidoEcommerce::estados())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('pedidos-ecommerce.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Valor total</th>
                                    <th>Valor do frete</th>
                                    <th>Desconto</th>
                                    <th>Qtd. itens</th>
                                    <th>Estado</th>
                                    <th>Data</th>
                                    <th>Tipo de pagamento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>#{{ $item->hash_pedido }}</td>
                                    <td>{{ $item->cliente->info }}</td>
                                    <td>{{ __moeda($item->valor_total) }}</td>
                                    <td>{{ __moeda($item->valor_frete) }}</td>
                                    <td>{{ __moeda($item->desconto) }}</td>
                                    <td>{{ sizeof($item->itens) }}</td>
                                    <td>{!! $item->_estado() !!}</td>
                                    <td>{{ __data_pt($item->created_at) }}</td>
                                    <td>{{ strtoupper($item->tipo_pagamento) }}</td>
                                    <td>
                                        <form action="{{ route('pedidos-ecommerce.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf
                                            
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            <a title="Visualizar" href="{{ route('pedidos-ecommerce.show', $item->id) }}" class="btn btn-dark btn-sm text-white">
                                                <i class="ri-survey-line"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
                
            </div>
        </div>
    </div>
</div>



@endsection

@section('js')
<script type="text/javascript">
    $(function(){

    });
</script>
@endsection

