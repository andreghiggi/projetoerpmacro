@extends('layouts.app', ['title' => 'Importação de Retorno'])
@section('css')
<style type="text/css">
    h5 strong{
        color: #27BCC2;
    }
</style>
@endsection
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Importação de Retorno</h4>
        <h5>{{ $banco }}</h5>
    </div>
    
    <form class="card-body" method="post" action="{{ route('remessa-boleto.import-save') }}">
        @csrf
        <div class="row">
            @forelse($data as $key => $item)
            <div class="card mt-1">
                <div class="card-header">
                    <h4>Pagador <strong>{{ $item->pagador }}</strong></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Documento <strong>{{ $item->documento }}</strong></h5>
                            <h5>Valor integral <strong>R$ {{ __moeda($item->valor_integral) }}</strong></h5>
                            <h5>Ocorrência <strong>{{ $item->ocorrencia }}</strong></h5>
                            <h5>Vencimento <strong>{{ $item->vencimento }}</strong></h5>
                        </div>
                        <div class="col-md-6">
                            <h5>Valor tarifa <strong>R$ {{ __moeda($item->valor_tarifa) }}</strong></h5>
                            <h5>Valor recebido <strong>R$ {{ __moeda($item->valor_recebido) }}</strong></h5>
                            <h5>Carteira <strong>{{ $item->carteira }}</strong></h5>
                            @if($item->conta_id)
                            <p class="text-danger">Conta vinculada</p>
                            @endif
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-8">
                            {!! Form::select('conta_id[]', 'Conta', ['' => 'Selecione'] + $contasPendentes->pluck('info', 'id')->all())
                            ->required()
                            ->id('conta_'.$key)
                            ->value($item->conta_id)
                            ->attrs(['class' => 'select2 form-select']) !!}
                        </div>

                        <div class="col-md-2">
                            {!! Form::tel('valor_recebido[]', 'Valor')
                            ->required()
                            ->value(__moeda($item->valor_recebido))
                            ->attrs(['class' => 'moeda']) !!}
                        </div>
                        
                        <div class="col-md-2">
                            {!! Form::date('data_recebimento[]', 'Data recebimento')
                            ->required()
                            ->value(date('Y-m-d')) !!}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <p>Nenhum detalhe de processamento!</p>
            @endforelse
        </div>
        @if(sizeof($data) > 0)
        <div class="col-12" style="text-align: right;">
            <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
        </div>
        @endif
    </form>
</div>
@endsection
