@extends('layouts.app', ['title' => 'Contas a Pagar'])
@section('css')
<style type="text/css">
    .badge:hover{
        cursor: pointer;
    }
</style>
@endsection
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('conta_pagar_create')
                    <a href="{{ route('conta-pagar.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova conta a pagar
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            {!!Form::select('fornecedor_id', 'Pesquisar por nome')->attrs(['class' => 'select2'])
                            ->options($fornecedor != null ? [$fornecedor->id => $fornecedor->info] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data Final')
                            !!}
                        </div>

                        @if(__countLocalAtivo() > 1)
                        <div class="col-md-2">
                            {!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        @endif
                        <div class="col-md-2">
                            {!!Form::select('status', 'Status', ['' => 'Todas', 1 => 'Pagas', 0 => 'Pendentes'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('ordem', 'Ordenar por', ['' => 'Data de cadastro', 1 => 'Data de vencimento'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('conta-pagar.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    @can('conta_pagar_delete')
                                    <th>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                        </div>
                                    </th>
                                    @endcan
                                    <th>Razão Social</th>
                                    <th>Descrição</th>
                                    @if(__countLocalAtivo() > 1)
                                    <th>Local</th>
                                    @endif
                                    <th>Valor Integral</th>
                                    <th>Valor Pago</th>
                                    <th>Data Cadastro</th>
                                    <th>Data Vencimento</th>
                                    <th>Data Pagamento</th>
                                    <th>Estado</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    @can('conta_pagar_delete')
                                    <td>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                                        </div>
                                    </td>
                                    @endcan
                                    <td>{{ $item->fornecedor ? $item->fornecedor->razao_social : '--' }}</td>
                                    <td>{{ $item->descricao }}</td>
                                    @if(__countLocalAtivo() > 1)
                                    <td class="text-danger">{{ $item->localizacao->descricao }}</td>
                                    @endif
                                    <td>{{ __moeda($item->valor_integral) }}</td>
                                    <td>{{ __moeda($item->valor_pago) }}</td>
                                    <td>{{ __data_pt($item->created_at, 0) }}</td>
                                    <td>
                                        {{ __data_pt($item->data_vencimento, 0) }}
                                        @if(!$item->status)
                                        <br>
                                        <span class="text-danger" style="font-size: 10px">{{ $item->diasAtraso() }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->status ? __data_pt($item->data_pagamento, false) : '--' }}</td>
                                    <td style="width: 200px;">
                                        @if($item->status)
                                        <span class="btn btn-success position-relative me-lg-5 btn-sm">
                                            <i class="ri-checkbox-line"></i> Pago
                                        </span>
                                        @else
                                        <span class="btn btn-warning position-relative me-lg-5 btn-sm">
                                            <i class="ri-alert-line"></i> Pendente
                                        </span>
                                        @if($item->motivo_estorno)
                                        <span onclick="motivoEstorno('{{ $item->motivo_estorno }}')" class="badge bg-primary">estornada</span>
                                        @endif
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('conta-pagar.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 150px;">
                                            @if(!$item->status)
                                            @method('delete')
                                            @can('conta_pagar_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('conta-pagar.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('conta_pagar_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                            @can('conta_pagar_edit')
                                            <a href="{{ route('conta-pagar.pay', $item) }}" class="btn btn-success btn-sm text-white">
                                                <i class="ri-money-dollar-box-line"></i>
                                            </a>
                                            @endcan
                                            
                                            @else
                                            @if(!$item->motivo_estorno)
                                            <a title="Estornar conta" href="{{ route('conta-pagar.estornar', $item) }}" class="btn btn-info btn-sm text-white">
                                                <i class="ri-arrow-go-back-fill"></i>
                                            </a>
                                            @endif
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">Soma da pagina</td>
                                    <td>{{ __moeda($data->sum('valor_integral')) }}</td>
                                    <td>{{ __moeda($data->sum('valor_pago')) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>

                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            @can('conta_pagar_delete')
                            <form action="{{ route('conta-pagar.destroy-select') }}" method="post" id="form-delete-select">
                                @method('delete')
                                @csrf
                                <div></div>
                                <button type="button" class="btn btn-danger btn-sm w-100 btn-delete-all" disabled>
                                    <i class="ri-close-circle-line"></i> Remover selecionados
                                </button>
                            </form>
                            @endcan
                        </div>

                        <div class="col-md-2">
                            @can('conta_pagar_edit')
                            <form action="{{ route('conta-pagar.pagar-select') }}" method="post" id="form-recebe-paga-select">
                                @csrf
                                <div></div>
                                <button type="button" class="btn btn-success btn-sm w-100 btn-recebe-paga-all" disabled>
                                    <i class="ri-check-line"></i> Pagar selecionados
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
                <br>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript" src="/js/delete_selecionados.js"></script>
<script type="text/javascript" src="/js/recebe_paga_selecionados.js"></script>
<script type="text/javascript">
    function motivoEstorno(motivo) {
        swal("", motivo, 'info')
    }
</script>
@endsection