@extends('layouts.app', ['title' => 'Detalhes da Conta'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h3>Detalhes da Conta</h3>
        <div style="text-align: right;" class="">
            <a href="{{ route('conta-pagar.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">

        <div class="pl-lg-4">
            <div class="row g-2">

                @if(__countLocalAtivo() > 1)
                <div class="col-md-2">
                    <label for="">Local</label>

                    <select id="inp-local_id" required class="select2 class-required" data-toggle="select2" name="local_id">
                        <option value="">Selecione</option>
                        @foreach(__getLocaisAtivoUsuario() as $local)
                        <option @isset($item) @if($item->local_id == $local->id) selected @endif @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input id="inp-local_id" type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
                @endif
                <div class="col-md-3">
                    {!!Form::text('', 'Descrição')->disabled()->value($item->descricao)
                    !!}
                </div>

                <div class="col-md-5">
                    {!!Form::text('', 'Fornecedor')->disabled()
                    ->value($item->fornecedor ? $item->fornecedor->razao_social : '')
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
                    {!!Form::date('', 'Data pagamento')->disabled()->value($item->data_pagamento)
                    !!}
                </div>
                @endif

                <div class="col-md-2">
                    {!!Form::select('status', 'Conta Paga', ['0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select'])->disabled()
                    ->value($item->status)
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::select('', 'Tipo Pagamento', App\Models\ContaReceber::tiposPagamento())->attrs(['class' => 'form-select'])->disabled()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::select('', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
                    ->attrs(['class' => 'form-select'])->disabled()
                    !!}
                </div>

                @if($item->contaEmpresa)
                <div class="col-md-2">
                    {!!Form::text('', 'Conta empresa')->disabled()->value($item->contaEmpresa->nome)
                    !!}
                </div>
                @endif

                <div class="col-md-4">
                    {!!Form::text('', 'Observação')->disabled()->value($item->observacao)
                    !!}
                </div>
                <div class="col-md-4">
                    {!!Form::text('', 'Observação 2')->disabled()->value($item->observacao2)
                    !!}
                </div>
                <div class="col-md-4">
                    {!!Form::text('', 'Observação 3')->disabled()->value($item->observacao3)
                    !!}
                </div>

                @if(isset($item) && $item->arquivo != null)
                <a href="{{ route('conta-pagar.download-file', [$item->id]) }}">
                    <i class="ri-file-download-line"></i>
                    Baixar arquivo
                </a>
                @endif


            </div>
        </div>

    </div>
</div>
@endsection
