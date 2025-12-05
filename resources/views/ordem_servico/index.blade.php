@extends('layouts.app', ['title' => 'Ordem Serviço'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('ordem_servico_create')
                    <a href="{{ route('ordem-servico.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Ordem de Serviço
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-2">
                        <div class="col-md-4">
                            {!!Form::select('cliente_id', 'Pesquisar por cliente')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data de início')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data de fim')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('codigo', 'Código')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('estado', 'Estado', ['' => 'Todos'] + \App\Models\OrdemServico::estados())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>


                        @if(!__isSegmentoPlanoOtica() && $configGeral->tipo_ordem_servico == 'oficina')
                        <div class="col-md-2">
                            {!!Form::select('veiculo_id', 'Veículo', ['' => 'Selecione'] + $veiculos->pluck('info', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            ->id('veiculo_id')
                            !!}
                        </div>
                        @endif

                        @if(__isSegmentoPlanoOtica())
                        <div class="col-md-2">
                            {!!Form::select('convenio_id', 'Convênio', ['' => 'Selecione'] + $convenios->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            ->id('convenio')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('situacao_entrega', 'Situação de entrega', ['' => 'Selecione', '1' => 'Entregue', '-1' => 'Não entregue'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('adiantamento', 'Adiantamento', ['' => 'Selecione', '1' => 'Com adiantamento', '-1' => 'Sem adiantamento'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        @endif
                        <div class="col-md-2 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('ordem-servico.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    @can('ordem_servico_delete')
                                    <th>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                        </div>
                                    </th>
                                    @endcan
                                    <th>Código</th>
                                    <th>Nome</th>
                                    <th>Data de início</th>
                                    <th>Data de entrega</th>
                                    <th>Valor</th>
                                    <th>Veículo</th>
                                    <th>Estado</th>
                                    <th>Situação de entrega</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    @can('ordem_servico_delete')
                                    <td data-label="Selecionar">
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                                        </div>
                                    </td>
                                    @endcan

                                    <td data-label="Código">{{ $item->codigo_sequencial }}</td>
                                    <td data-label="Nome">{{ $item->cliente->razao_social }}</td>
                                    <td data-label="Data de início">{{ __data_pt($item->data_inicio, 1) }}</td>
                                    <td data-label="Data de entrega">{{ $item->data_entrega ? __data_pt($item->data_entrega, 0) : '' }}</td>
                                    <td data-label="Valor">{{ __moeda($item->valor) }}</td>
                                    <td data-label="Veículo">{{ $item->veiculo ? $item->veiculo->info : '--' }}</td>

                                    <td data-label="Estado">
                                        @if($item->estado == 'pd')
                                        <span class="badge bg-warning">PENDENTE</span>
                                        @elseif($item->estado == 'ap')
                                        <span class="badge bg-success">APROVADO</span>
                                        @elseif($item->estado == 'rp')
                                        <span class="badge bg-danger">REPROVADO</span>
                                        @elseif($item->estado == 'fz')
                                        <span class="badge bg-info">FINALIZADO</span>
                                        @endif
                                    </td>

                                    <td data-label="Situação de entrega">
                                        @if($item->data_entrega != '')
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>

                                    <td>
                                        <form style="width: 180px;" action="{{ route('ordem-servico.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            @can('ordem_servico_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('ordem-servico.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan

                                            @if($item->estado == 'pd' || $item->estado == 'rp')
                                            @can('ordem_servico_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                            @endif

                                            <a title="Visualizar" href="{{ route('ordem-servico.show', $item->id) }}" class="btn btn-dark btn-sm text-white">
                                                <i class="ri-survey-line"></i>
                                            </a>

                                            <a class="btn btn-primary btn-sm" href="{{ route('ordem-servico.duplicar', [$item->id]) }}" title="Duplicar OS">
                                                <i class="ri-file-copy-line"></i>
                                            </a>

                                            @if(__isSegmentoPlanoOtica())
                                            <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-printer-line"></i>
                                                <span class="caret"></span>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a target="_blank" class="dropdown-item" href="{{ route('ordem-servico.print-otica', ['ordem_servico_id='.$item->id, 'via_cliente=1']) }}">Imprimir via do cliente</a>
                                                <a target="_blank" class="dropdown-item" href="{{ route('ordem-servico.print-otica', ['ordem_servico_id='.$item->id, 'via_laboratorio=1']) }}">Imprimir via do laboratório</a>
                                                <a target="_blank" class="dropdown-item" href="{{ route('ordem-servico.print-otica', ['ordem_servico_id='.$item->id, 'os=1']) }}">Imprimir OS</a>
                                                <a target="_blank" class="dropdown-item" href="{{ route('ordem-servico.print-otica', ['ordem_servico_id='.$item->id, 'os=1', 'via_cliente=1']) }}">Imprimir OS + via do cliente</a>
                                                <a target="_blank" class="dropdown-item" href="{{ route('ordem-servico.print-otica', ['ordem_servico_id='.$item->id, 'os=1', 'via_laboratorio=1']) }}">Imprimir OS + via do laboratório</a>
                                                <a target="_blank" class="dropdown-item" href="{{ route('ordem-servico.print-otica', ['ordem_servico_id='.$item->id, 'via_cliente=1', 'via_laboratorio=1']) }}">Via do cliente + via do laboratório</a>
                                            </div>
                                            @endif
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
                        @can('ordem_servico_delete')
                        <form action="{{ route('ordem-servico.destroy-select') }}" method="post" id="form-delete-select">
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
                <br>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript" src="/js/delete_selecionados.js"></script>
@endsection
