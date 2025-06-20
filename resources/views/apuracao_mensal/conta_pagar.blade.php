@extends('layouts.app', ['title' => 'Adicionar ao conta a pagar'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-lg-12">
                    {!!Form::open()->fill($item)
                    ->route('apuracao-mensal.set-conta', [$item->id])
                    ->put()
                    !!}
                    <div class="row mt-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                {!!Form::text('descricao', 'Descrição')->value('Pagamento '. $item->funcionario->nome)
                                !!}
                            </div>
                            {{-- <div class="col-md-4">
                                {!!Form::select('fornecedor_id', 'Fornecedor')->attrs(['class' => 'select2'])->required()->options(isset($item->fornecedor_id) ? [$item->fornecedor->razao_social] : [])
                                !!}
                            </div> --}}
                            <div class="col-md-2">
                                {!!Form::text('valor_integral', 'Valor Integral')->attrs(['class' => 'moeda'])->value( __moedaInput($item->valor_final))->required()
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::date('data_vencimento', 'Data Vencimento')->required()
                                !!}
                            </div>

                            <div class="col-md-2">
                                {!!Form::select('status', 'Conta Paga', ['0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select'])->required()
                                !!}
                            </div>

                            <div class="col-md-3">
                                {!!Form::select('tipo_pagamento', 'Tipo Pagamento', App\Models\ContaReceber::tiposPagamento())->attrs(['class' => 'form-select'])->required()
                                !!}
                            </div>

                            <hr class="mt-4">
                            <div class="col-12" style="text-align: right;">
                                <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
                            </div>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection