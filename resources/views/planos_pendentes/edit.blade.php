@extends('layouts.app', ['title' => 'Atribuir Plano'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Atribuir Plano</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('planos-pendentes.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('planos-pendentes.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            <div class="row g-2">

                <div class="col-md-4">
                    {!!Form::text('empresa', 'Empresa')
                    ->required()
                    ->value($item->empresa->nome)
                    ->readonly()
                    !!}
                </div>
                <div class="col-md-4">
                    {!!Form::text('contador', 'Contador')
                    ->required()
                    ->value($item->contador->nome)
                    ->readonly()
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::text('plano', 'Plano')
                    ->required()
                    ->value($item->plano->nome)
                    ->readonly()
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::tel('valor', 'Valor')
                    ->required()
                    ->value(__moeda($item->valor))
                    ->attrs(['class' => 'moeda'])
                    !!}
                </div>

                <div class="col-md-3">
                    {!!Form::select('forma_pagamento', 'Forma de pagamento', \App\Models\Plano::formasPagamento())
                    ->required()
                    ->attrs(['class' => 'select2'])
                    !!}
                </div>

                <div class="col-md-3 mt-2">
                    {!!Form::select('status_pagamento', 'Status de pagamento', \App\Models\FinanceiroPlano::statusDePagamentos())
                    ->required()
                    ->attrs(['class' => 'select2'])
                    ->value('recebido')
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

@section('js')
<script src="/js/mdfe.js"></script>
@endsection

@endsection
