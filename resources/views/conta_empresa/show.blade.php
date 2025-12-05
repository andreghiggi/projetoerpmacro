@extends('layouts.app', ['title' => 'Movimentações conta ' . $item->nome])
@section('content')

<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">


                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">

                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data Final')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('tipo', 'Tipo', ['' => 'Todos', 'entrada' => 'Entrada', 'saida' => 'Saída'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('contas-empresa.show', [$item->id]) }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>

                <div style="text-align: right; margin-top: -35px;">
                    <form method="get" action="{{ route('contas-empresa.print', [$item->id]) }}">

                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                        <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                        <button class="btn btn-dark btn-sm px-3">
                            <i class="ri-printer-line"></i> Imprimir
                        </button>
                    </form>
                </div>
                <br>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    Conta: <strong class="text-primary">{{ $item->nome }}</strong>
                                </div>
                                <div class="col-md-3">
                                    Banco: <strong class="text-primary">{{ $item->banco }}</strong>
                                </div>
                                <div class="col-md-3">
                                    Agência: <strong class="text-primary">{{ $item->agencia }}</strong>
                                </div>
                                <div class="col-md-3">
                                    Conta corrente: <strong class="text-primary">{{ $item->conta }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    @forelse($data as $m)
                    <div class="row">
                        <div class="col-md-2 text-muted">
                            {{ __data_pt($m->created_at) }}
                        </div>

                        <div class="col-md-2 text-dark">
                            @if($m->fornecedor)
                            {{ $m->fornecedor->razao_social }}
                            @endif

                            @if($m->cliente)
                            {{ $m->cliente->razao_social }}
                            @endif
                        </div>

                        <div class="col-md-4 col-12 text-muted">
                            {{ $m->descricao }}
                            @if($m->caixa_id)
                            <br>
                            Fechamento caixa abertura {{ __data_pt($m->caixa->created_at) }}
                            @endif
                        </div>

                        <div class="col-md-2 col-12 @if($m->tipo == 'entrada') text-success @else text-danger @endif">
                            <label class="float-right">{{ $m->tipo_pagamento ? App\Models\Nfce::getTipoPagamento($m->tipo_pagamento) : '' }}</label>
                        </div>

                        <div class="col-md-2 col-12 @if($m->tipo == 'entrada') text-success @else text-danger @endif">
                            <label class="float-right">@if($m->tipo == 'entrada')+@else-@endif R$ {{ __moeda($m->valor) }}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            @if($m->categoria)
                            <span>Categoria <strong class="text-dark">{{ $m->categoria->nome }}</strong></span>
                            @endif
                        </div>
                        <div class="col-md-8">
                            @if($m->numero_documento)
                            <span>Nº NFe <strong class="text-dark">{{ $m->numero_documento }}</strong></span>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <label class="float-right @if($m->saldo_atual <= 0) text-danger @else text-primary @endif">
                                Saldo: R$ {{ __moeda($m->saldo_atual) }}
                            </label>
                        </div>
                    </div>
                    <hr>
                    @empty
                    <h4 class="text-center">Nenhuma movimentação encontrada!</h4>
                    @endforelse
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
