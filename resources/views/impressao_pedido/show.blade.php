@extends('layouts.app', ['title' => 'Controle de Impressão - ' . $item->descricao])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <i class="ri-printer-line text-primary" style="font-size: 70px;"></i>
                    </div>
                    <div class="col-md-10">
                        <br>
                        <h5>Tela de impressão de pedidos</h5>
                        <h4 class="text-primary">{{ $item->descricao }}</h4>
                    </div>
                </div>
                <hr>
                <div class="row logs">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="impressora_id" value="{{ $item->id }}">
<input type="hidden" id="timeout" value="{{ $item->requisicao_segundos }}">
@endsection
@section('js')
<script type="text/javascript" src="/js/controle_impressao_pedido.js"></script>
@endsection
