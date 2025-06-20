@extends('layouts.app', ['title' => 'Visualizando Boleto'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Visualizando Boleto</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('boleto.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <h4>Cliente: <strong class="text-success">{{ $item->contaReceber->cliente->info }}</strong></h4>
        <h4>Valor: <strong class="text-success">{{ __moeda($item->valor) }}</strong></h4>
        <h4>Vencimento: <strong class="text-success">{{ __data_pt($item->vencimento, 0) }}</strong></h4>
        <h4>Data de registro: <strong class="text-success">{{ __data_pt($item->created_at) }}</strong></h4>

        <a target="_blank" class="btn btn-dark" href="{{ route('boleto.print', [$item->id]) }}">
            <i class="ri-printer-line"></i>
            Imprimir
        </a>
    </div>
</div>
@endsection
