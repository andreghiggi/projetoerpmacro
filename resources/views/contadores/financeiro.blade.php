@extends('layouts.app', ['title' => 'Financeiro Contador'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <a href="{{ route('contadores.financeiro-create', [$item->id]) }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Pagamento
                    </a>

                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-2">
                            {!!Form::select('mes', 'Pesquisar por mês', ['' => 'Selecione'] + \App\Models\FinanceiroContador::meses())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            <label>Pesquisar por ano</label>
                            <select class="form-select" name="ano">
                                @foreach(\App\Models\FinanceiroContador::anos() as $key => $a)
                                <option @if(request()->ano == $a) selected @else @if(date('Y') == $a) selected @endif @endif value="{{$a}}">{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('contadores.financeiro', [$item->id]) }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Mês/Ano</th>
                                    <th>Valor de venda</th>
                                    <th>Valor da comissão</th>
                                    <th>% de comissão</th>
                                    <th>Tipo de pagamento</th>
                                    <th>Observação</th>
                                    <th>Status de pagamento</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($financeiro as $i)
                                <tr>
                                    <td>{{ $i->mes }}/{{ $i->ano }}</td>
                                    <td>{{ __moeda($i->total_venda) }}</td>
                                    <td>{{ __moeda($i->valor_comissao) }}</td>
                                    <td>{{ $i->percentual_comissao }}</td>
                                    <td>{{ $i->tipo_pagamento }}</td>
                                    <td>{{ $i->observacao }}</td>
                                    <td>
                                        @if($i->status_pagamento)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('contadores-financeiro.destroy', [$i->id]) }}" method="post" id="form-{{$i->id}}" style="width: 100px;">
                                            @method('delete')
                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>


                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td>Soma</td>
                                    <td>R$ {{ __moeda($financeiro->sum('total_venda')) }}</td>
                                    <td>R$ {{ __moeda($financeiro->sum('valor_comissao')) }}</td>
                                    <td colspan="5"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
