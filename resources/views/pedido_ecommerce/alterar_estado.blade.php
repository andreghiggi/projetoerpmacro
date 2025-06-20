@extends('layouts.app', ['title' => 'Alterar Estado do Pedido'])
@section('content')

<div class="card mt-1">
    <div class="card-body">
        <div class="pl-lg-4">
            {!!Form::open()->fill($item)
            ->put()
            ->route('pedidos-ecommerce.update', [$item->id])
            !!}
            <div class="pl-lg-4">
                <div class="row g-2">
                    <div class="col-md-2">
                        {!!Form::select('estado', 'Estado', [
                        'novo' => 'Novo', 'preparando' => 'Preparando', 'em_trasporte' => 'Em transporte', 
                        'finalizado' => 'Finalizado', 'recusado' => 'Recusado'
                        ])->required()->attrs(['class' => 'form-select'])
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::tel('valor_frete', 'Valor do frete')->required()
                        ->attrs(['class' => 'moeda'])
                        ->value(__moeda($item->valor_frete))
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::date('data_entrega', 'Data de entrega')
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::text('codigo_rastreamento', 'Código de rastreamento')
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::select('status_pagamento', 'Status de pagamento', [
                        'approved' => 'Aprovado', 'pending' => 'Pendente'
                        ])->required()->attrs(['class' => 'form-select'])
                        !!}
                    </div>

                    <div class="col-12" style="text-align: right;">
                        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
                    </div>
                </div>
            </div>
            {!!Form::close()!!}
        </div>
    </div>
</div>
@endsection