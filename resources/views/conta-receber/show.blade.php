@extends('layouts.app', ['title' => 'Detalhes da Conta'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h3>Detalhes da Conta</h3>
        <div style="text-align: right;" class="">
            <a href="{{ route('conta-receber.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">

        <div class="pl-lg-4">
            <div class="row g-2">


                @if(__countLocalAtivo() > 1)
                <div class="col-md-2">
                    {!!Form::text('', 'Local')->disabled()->value($item->localizacao->descricao)
                    !!}
                </div>
                @endif

                <div class="col-md-3">
                    {!!Form::text('descricao', 'Descrição')->disabled()->value($item->descricao)
                    !!}
                </div>
                <div class="col-md-5">
                    {!!Form::text('', 'Cliente')->disabled()
                    ->value($item->cliente ? $item->cliente->razao_social : '')
                    !!}
                </div>
               
                <div class="col-md-2">
                    {!!Form::text('', 'Valor Integral')->value(__moeda($item->valor_integral))->disabled()
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::date('', 'Data Vencimento')->disabled()->value($item->data_vencimento)
                    !!}
                </div>
                 @if($item->status)
                <div class="col-md-2">
                    {!!Form::date('', 'Data recebimento')->disabled()->value($item->data_recebimento)
                    !!}
                </div>
                @endif

                <div class="col-md-2">
                    {!!Form::text('', 'Conta Recebida')->value(($item->status ? 'Sim' : 'Não'))->disabled()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::select('', 'Tipo Pagamento', App\Models\ContaReceber::tiposPagamento())->attrs(['class' => 'form-select'])->disabled()->value($item->tipo_pagamento)
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::select('categoria_conta_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
                    ->attrs(['class' => 'form-select'])->disabled()->value($item->categoria_conta_id)
                    !!}
                </div>

                @if($item->contaEmpresa)
                <div class="col-md-2">
                    {!!Form::text('', 'Conta empresa')->disabled()->value($item->contaEmpresa->nome)
                    !!}
                </div>
                @endif

                <div class="col-md-4">
                    {!!Form::text('observacao', 'Observação')->disabled()->value($item->observacao)
                    !!}
                </div>
                <div class="col-md-4">
                    {!!Form::text('observacao2', 'Observação 2')->disabled()->value($item->observacao2)
                    !!}
                </div>
                <div class="col-md-4">
                    {!!Form::text('observacao3', 'Observação 3')->disabled()->value($item->observacao3)
                    !!}
                </div>

                @if(isset($item) && $item->arquivo != null)
                <a href="{{ route('conta-receber.download-file', [$item->id]) }}">
                    <i class="ri-file-download-line"></i>
                    Baixar arquivo
                </a>
                @endif
            </div>

        </div>
    </div>
    @endsection

    @section('js')
    <script type="text/javascript">

    </script>
    @endsection

