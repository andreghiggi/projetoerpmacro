@extends('layouts.app', ['title' => 'Lista de Preços'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @can('lista_preco_create')
                        <a href="{{ route('lista-preco.create') }}" class="btn btn-success">
                            <i class="ri-add-circle-fill"></i>
                            Nova Lista
                        </a>
                        @endcan
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-2">
                        <h5>Total de produtos cadastrados: <strong class="text-success">{{ $totalDeProdutos }}</strong></h5>
                    </div>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::text('nome', 'Pesquisar por nome')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('tipo_pagamento', 'Tipo de pagamento', ['' => 'Selecione'] + App\Models\ListaPreco::tiposPagamento())->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-3">
                            {!!Form::select('funcionario_id', 'Funcionário')
                            ->options((isset($item) && $item->funcionario) ? [$item->funcionario_id => $item->funcionario->nome] : [])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('lista-preco.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    @can('lista_preco_delete')
                                    <th>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                        </div>
                                    </th>
                                    @endcan
                                    <th style="width: 25%">Nome</th>
                                    <th>Ajuste sobre</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>% Alteração</th>
                                    <th>Data de cadastro</th>
                                    <th>Tipo de pagamento</th>
                                    <th>Funcionário</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    @can('lista_preco_delete')
                                    <td data-label="Selecionar">
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                                        </div>
                                    </td>
                                    @endcan

                                    <td data-label="Nome">{{ $item->nome }}</td>

                                    <td data-label="Ajuste sobre">
                                        {{ $item->ajuste_sobre == 'valor_venda' ? 'Valor de venda' : 'Valor de compra' }}
                                    </td>

                                    <td data-label="Tipo">
                                        {{ $item->tipo == 'incremento' ? 'Incremento' : 'Redução' }}
                                    </td>

                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>

                                    <td data-label="% Alteração">{{ $item->percentual_alteracao }}%</td>

                                    <td data-label="Data de cadastro">{{ __data_pt($item->created_at) }}</td>

                                    <td data-label="Tipo de pagamento">{{ $item->tipo_pagamento ? $item->getTipoPagamento() : '' }}</td>

                                    <td data-label="Funcionário">{{ $item->funcionario ? $item->funcionario->nome : '' }}</td>

                                    <td>
                                        <form style="width: 150px;" action="{{ route('lista-preco.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('lista_preco_edit')
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('lista-preco.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('lista_preco_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                            <a title="Ver produtos" class="btn btn-dark btn-sm text-white" href="{{ route('lista-preco.show', [$item->id]) }}">
                                                <i class="ri-file-list-2-fill"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <br>
                        @can('lista_preco_delete')
                        <form action="{{ route('lista-preco.destroy-select') }}" method="post" id="form-delete-select">
                            @method('delete')
                            @csrf
                            <div></div>
                            <button type="button" class="btn btn-danger btn-sm btn-delete-all" disabled>
                                <i class="ri-close-circle-line"></i> Remover selecionados
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script type="text/javascript" src="/js/delete_selecionados.js"></script>
@endsection