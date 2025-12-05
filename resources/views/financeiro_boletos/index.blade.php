@extends('layouts.app', ['title' => 'Financeiro Boletos'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('financeiro-boleto.create') }}" class="btn btn-success">
                    <i class="ri-add-circle-fill"></i>
                    Novo Boleto
                </a>
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
                            {!!Form::date('start_date', 'Data inicial venc.')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final venc.')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('status_pagamento', 'Status de pagamento', ['' => 'Selecione', 1 => 'Recebido', 0 => 'Pendente'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('financeiro-boleto.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
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
                                    <th>Data de vencimento</th>
                                    <th>Data de cadastro</th>
                                    <th>Status de recebimento</th>

                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>

                                    <td>{{ $item->empresa->info }}</td>
                                    <td>{{ $item->plano ? $item->plano->nome : '' }}</td>
                                    <td>{{ __moeda($item->valor) }}</td>

                                    <td>{{ __data_pt($item->vencimento, 0) }}</td>
                                    <td>{{ __data_pt($item->created_at, 1) }}</td>
                                    <td>
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>

                                        <form action="{{ route('financeiro-boleto.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('financeiro-boleto.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>

                                            <a target="_blank" title="Imprimir boleto" class="btn btn-dark btn-sm" href="{{ $item->pdf_boleto }}">
                                                <i class="ri-printer-line"></i>
                                            </a>


                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


                {!! $data->appends(request()->all())->links() !!}

            </div>
        </div>
    </div>
</div>

@endsection

