@extends('layouts.app',['title'=>'Dashboard de Compras Inteligente'])

@section('content')
<div class="mb-3">
    <h4>Dashboard Inteligente de Compras</h4>
</div>

<div class="row text-center mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm"><div class="card-body">
            <h6>Produtos Críticos</h6>
            <h3>{{ collect($dados)->where('estoque_atual','<','estoque_minimo')->count() }}</h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <h6>Consumo Alto</h6>
                <h3>{{ collect($dados)->where('media_consumo_dia','>',1)->count() }}</h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <h6>Lead Time Médio</h6>
                <h3>{{ round(collect($dados)->avg('lead_time'),1) }} dias</h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <h6>Produtos com fornecedor</h6>
                <h3>{{ collect($dados)->where('ultimo_fornecedor','!=',null)->count() }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table">
        <thead class="table-dark">
            <tr>
                <th>Produto</th>
                <th>Estoque</th>
                <th>Mínimo</th>
                <th>Consumo/dia</th>
                <th>Lead Time</th>
                <th>Último Fornecedor</th>
                <th>Custo</th>
                <th>Sugerir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dados as $d)
            <tr @if($d->estoque_atual < $d->estoque_minimo) class="table-danger" @endif>
                <td>{{ $d->nome }}</td>
                <td>{{ $d->estoque_atual }}</td>
                <td>{{ $d->estoque_minimo }}</td>
                <td>{{ number_format($d->media_consumo_dia,2) }}</td>
                <td>{{ (int)$d->lead_time }} dias</td>
                <td>{{ $d->ultimo_fornecedor }}</td>
                <td>R$ {{ __moeda($d->ultimo_valor) }}</td>
                <td><button class="btn btn-sm btn-primary sugerir" data-id="{{ $d->id }}">IA</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalIA" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content p-3">
        <div id="ia_resposta">Processando...</div>
    </div></div>
</div>

@endsection

@section('js')
<script>
    $('.sugerir').click(function(){
        let id = $(this).data('id')
        $('#modalIA').modal('show')
        $('#ia_resposta').html('Analisando...')

        $.post("{{ route('compras.ia.analisar') }}",{produto_id:id,_token:'{{csrf_token()}}'})
        .done(function(r){
            console.log(r.ia)
            $('#ia_resposta').html('<h5>'+r.dados.nome+'</h5><div>'+r.ia+'</div>')
        })
        .fail(function(err){
            console.log(err)
            $('#ia_resposta').html('<div class="text-danger">Falha ao consultar IA</div>')
        })
    })
</script>
@endsection
