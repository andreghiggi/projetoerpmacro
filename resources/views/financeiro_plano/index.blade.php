@extends('layouts.app', ['title' => 'Financeiro Planos'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}

                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('empresa', 'Pesquisar por empresa')
                            ->options($empresa ? [$empresa->id => $empresa->info] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('status_pagamento', 'Status de pagamento', ['' => 'Selecione'] + \App\Models\FinanceiroPlano::statusDePagamentos())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('financeiro-plano.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Empresa</th>
                                    <th>Plano</th>
                                    <th>Valor</th>
                                    <th>Tipo de pagamento</th>
                                    <th>Data de cadastro</th>
                                    <th>Status</th>

                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>

                                    <td>{{ $item->empresa->info }}</td>
                                    <td>{{ $item->plano->nome }}</td>
                                    <td>{{ __moeda($item->valor) }}</td>
                                    <td>
                                        {{ $item->tipo_pagamento }}
                                    </td>

                                    <td>{{ __data_pt($item->created_at, 1) }}</td>
                                    <td>{{ strtoupper($item->status_pagamento) }}</td>
                                    <td>

                                        <form action="{{ route('financeiro-plano.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>

                                            <a class="btn btn-warning btn-sm" href="{{ route('financeiro-plano.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <h5>Soma recebido: <strong class="text-success">R$ {{ __moeda($somaRecebido) }}</strong></h5>
                    </div>
                    <div class="col-md-4">
                        <h5>Soma pendente: <strong class="text-warning">R$ {{ __moeda($somaPendente) }}</strong></h5>
                    </div>
                    <div class="col-md-4">
                        <h5>Soma cancelado: <strong class="text-danger">R$ {{ __moeda($somaCancelado) }}</strong></h5>
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}

            </div>
        </div>
    </div>
</div>

@endsection

