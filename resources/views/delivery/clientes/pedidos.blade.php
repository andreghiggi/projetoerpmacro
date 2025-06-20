@extends('layouts.app', ['title' => 'Pedidos'])
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

<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <div style="text-align: right;">
                    <a href="{{ route('clientes-delivery.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                <div class="row mt-3">
                    @forelse($cliente->pedidos as $item)
                    <a class="col-12 col-lg-4" href="{{ route('pedidos-delivery.show', [$item->id]) }}">
                        <div class="card">

                            <div class="card-body" style="height: 200px">
                                <h3 class="card-title">ID: <strong>#{{ $item->id }}</strong></h3>

                                <h4>Total: <strong>R$ {{ __moeda($item->valor_total) }}</strong></h4>
                                <h4>Cliente: <strong>{{ $item->cliente->razao_social }}</strong></h4>
                                <h4>Total de itens: <strong>{{ sizeof($item->itens) }}</strong></h4>
                                @if($item->endereco)
                                <h4>Endereço: <strong class="text-primary">{{ $item->endereco->info }}</strong></h4>
                                @else
                                <h4 class="text-primary">Retirada no balcão</h4>
                                @endif
                            </div>
                        </div>
                    </a>
                    @empty
                    <p class="text-center text-primary">Nenhum pedido para este cliente</p>
                    @endforelse
                </div>
                
            </div>
        </div>
    </div>
</div>

@endsection

